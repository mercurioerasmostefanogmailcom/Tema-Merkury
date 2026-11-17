<?php
/** Tema Merkury per MercurioCMS — adattamento autonomo di MERKURY 0.6.2. */
if (!defined('CMS_VERSION')) { return; }
if (!function_exists('merkury_cms_defaults')) {
    function merkury_cms_defaults(): array {
        return [
            'preset'=>'chronological','intro_eyebrow'=>'','intro_title'=>'Forever on the road','intro_text'=>'','show_intro'=>'1',
            'hero_image'=>'','hero_overlay'=>'55','hero_fit'=>'cover','hero_height'=>'480',
            'brand_mark_image'=>'','native_colors'=>[],
            'primary_color'=>'#174A78','accent_color'=>'#4D91C6','ink_color'=>'#10243A','paper_color'=>'#F7FAFC','chrome_color'=>'#174A78',
            'posts_per_page'=>'10','show_sidebar'=>'1','header_style'=>'prominent','footer_text'=>'Un diario, una storia alla volta.',
            'color_mode'=>'system','show_credits'=>'1','automatic_backup'=>'1','category_meta'=>[],'wizard_completed'=>'0'
        ];
    }

    function merkury_cms_settings(): array {
        $d=merkury_cms_defaults();
        $raw=json_decode(cms_setting('merkury_theme_settings','{}'),true);
        $s=is_array($raw)?array_merge($d,$raw):$d;
        foreach(['primary_color','accent_color','ink_color','paper_color','chrome_color'] as $k) {
            if(!preg_match('/^#[0-9a-fA-F]{6}$/',(string)$s[$k])) $s[$k]=$d[$k];
        }
        // Le impostazioni native condivise hanno sempre precedenza sui valori storici
        // eventualmente presenti nel JSON del tema: MercurioCMS è la fonte unica.
        $nativeMap = [
            'primary_color'=>'color_primary', 'paper_color'=>'color_background',
            'ink_color'=>'color_text', 'chrome_color'=>'color_header_bg',
            'accent_color'=>'color_hero_eyebrow', 'intro_eyebrow'=>'hero_eyebrow_text', 'intro_title'=>'hero_title_text',
            'intro_text'=>'site_description'
        ];
        foreach ($nativeMap as $themeKey=>$cmsKey) {
            $fallback = ['hero_title_text'=>'Benvenuto','hero_eyebrow_text'=>'Sito web','site_description'=>''][$cmsKey] ?? '';
            $native = cms_setting($cmsKey, $fallback);
            if (in_array($cmsKey, ['hero_title_text','hero_eyebrow_text','site_description'], true) || $native !== '') $s[$themeKey] = $native;
        }
        $s['site_name'] = cms_setting('site_name', 'MercurioCMS');
        $s['site_logo'] = cms_setting('site_logo', '');
        $s['site_favicon'] = cms_setting('site_favicon', '');
        // Scarta le chiavi duplicate presenti nelle configurazioni delle versioni precedenti.
        unset($s['theme_logo'], $s['theme_favicon']);
        $s['native_colors'] = [];
        $nativeDefaults=['color_surface'=>'#FFFFFF','color_footer_bg'=>'#173F31','color_footer_text'=>'#FFFFFF','color_header_text'=>'#FFFFFF','color_hero_bg'=>'#F3F6F4','color_hero_title'=>'#173F31','color_hero_text'=>'#24332B'];
        foreach ($nativeDefaults as $key=>$defaultColor) {
            $nativeColor=cms_setting($key, $defaultColor);
            $s['native_colors'][$key]=preg_match('/^#[0-9a-fA-F]{6}$/',$nativeColor)?strtoupper($nativeColor):$defaultColor;
        }
        foreach (['primary_color'=>'#245B45','paper_color'=>'#F3F6F4','ink_color'=>'#24332B','chrome_color'=>'#173F31','accent_color'=>'#245B45'] as $key=>$fallbackColor) {
            if (!preg_match('/^#[0-9a-fA-F]{6}$/',(string)$s[$key])) $s[$key]=$fallbackColor;
        }
        $s['preset']=in_array($s['preset'],['chronological','photographic'],true)?$s['preset']:'chronological';
        $s['color_mode']=in_array($s['color_mode'],['light','dark','system'],true)?$s['color_mode']:'system';
        $s['hero_fit']=$s['hero_fit']==='contain'?'contain':'cover';
        $s['hero_overlay']=(string)max(0,min(85,(int)$s['hero_overlay']));
        $s['hero_height']=(string)max(240,min(900,(int)$s['hero_height']));
        $s['posts_per_page']=(string)max(1,min(30,(int)$s['posts_per_page']));
        foreach(['show_intro','show_sidebar','show_credits','wizard_completed'] as $k) $s[$k]=!empty($s[$k])?'1':'0';
        foreach(['hero_image','brand_mark_image'] as $k) {
            if(!preg_match('~^(uploads/images/[A-Za-z0-9._/-]+|https?://[^\s"\']+)$~i',(string)$s[$k])) $s[$k]='';
        }
        if(!is_array($s['category_meta'])) $s['category_meta']=[];
        return $s;
    }

    function merkury_cms_extract_element(string $html,string $tag): ?array {
        if(!preg_match_all('~</?'.preg_quote($tag,'~').'\b[^>]*>~i',$html,$matches,PREG_OFFSET_CAPTURE)) return null;
        $depth=0; $start=-1; $openEnd=0;
        foreach($matches[0] as $match) {
            $token=$match[0]; $pos=$match[1];
            if(str_starts_with($token,'</')) {
                $depth--;
                if($depth===0 && $start>=0) {
                    $end=$pos+strlen($token);
                    return ['full'=>substr($html,$start,$end-$start),'inner'=>substr($html,$openEnd,$pos-$openEnd)];
                }
            } else {
                if($depth===0) { $start=$pos; $openEnd=$pos+strlen($token); }
                $depth++;
            }
        }
        return null;
    }

    /** Duplica la navigazione delle pagine in fondo all'elenco, mantenendo intatti i link originali. */
    function merkury_cms_add_bottom_pagination(string $main): string {
        if (stripos($main, 'page-list') === false) return $main;
        $pagination = '';
        // Prima scelta: contenitore esplicitamente identificato come paginazione.
        if (preg_match('~<(nav|div|ul)\b(?=[^>]*\bclass\s*=\s*(["\'])[^"\']*(?:pagination|pager|page-nav|pagination-links)[^"\']*\2)[^>]*>.*?</\1\s*>~is', $main, $m)) {
            $pagination = $m[0];
        } else {
            // Fallback per markup CMS che non assegna una classe specifica al contenitore.
            if (preg_match('~<(nav|div)\b[^>]*>(?:(?!</?\1\b).)*?(?:Successivo|Precedente|Next|Previous)(?:(?!</?\1\b).)*?</\1\s*>~is', $main, $m)) {
                $pagination = $m[0];
            }
        }
        if ($pagination === '' || stripos($main, 'merkury-bottom-pagination') !== false) return $main;
        $copy = preg_replace('~\b(id)=((["\'])[^"\']*\3)~i', '', $pagination) ?? $pagination;
        $copy = '<div class="merkury-bottom-pagination">'.$copy.'</div>';
        // Inserisce la copia alla fine della sezione che contiene l'elenco, senza spostare quella superiore.
        if (preg_match('~(<section\b(?=[^>]*\bclass\s*=\s*(["\'])[^"\']*\bpage-list\b[^"\']*\2)[^>]*>)(.*?)(</section\s*>)~is', $main, $section, PREG_OFFSET_CAPTURE)) {
            $full = $section[0][0];
            $closing = $section[4][0];
            $updated = substr($full, 0, strlen($full) - strlen($closing)).$copy.$closing;
            $main = substr_replace($main, $updated, $section[0][1], strlen($full));
        }
        return $main;
    }

    function merkury_cms_h($v): string { return htmlspecialchars((string)$v,ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8'); }

    /** URL assoluto rispetto alla radice effettiva del CMS, anche se installato in sottocartella. */
    function merkury_cms_asset_url(string $path): string {
        $path=trim($path);
        if($path==='' || preg_match('~^https?://~i',$path)) return $path;
        $script=str_replace('\\','/',(string)($_SERVER['SCRIPT_NAME']??'/index.php'));
        // Anche quando la funzione viene richiamata dal pannello del plugin,
        // risali alla vera radice del CMS (non alla cartella /plugins/...).
        if(preg_match('~^(.*?)/plugins/.*$~',$script,$m)) $root=$m[1];
        else { $root=dirname($script); if($root==='.' || $root==='/') $root=''; }
        return rtrim($root,'/').'/'.ltrim($path,'/');
    }

    function merkury_cms_url(string $path): string {
        return merkury_cms_asset_url('plugins/merkury-theme/'.$path);
    }

    function merkury_cms_admin_url(string $file='admin.php'): string {
        $script=str_replace('\\','/',(string)($_SERVER['SCRIPT_NAME']??'/index.php'));
        $root=preg_replace('~/plugins/.*$~','',$script);
        if($root===$script) $root=rtrim(dirname($script),'/');
        return rtrim($root,'/').'/plugins/merkury-theme/'.ltrim($file,'/');
    }

    function merkury_cms_register(): void {
        cms_register_admin_menu('merkury-theme','Aspetto Merkury','../plugins/merkury-theme/admin.php');
    }
    merkury_cms_register();

    /** URL archivio: la homepage del tema è sempre l'elenco cronologico, mai una pagina statica CMS. */
    function merkury_cms_archive_url(int $page=1, string $month='', string $category=''): string {
        $args=[];
        if($page>1) $args['mk_page']=$page;
        if($month!=='') $args['mk_month']=$month;
        if($category!=='') $args['category']=$category;
        return 'index.php'.($args?'?'.http_build_query($args,'','&',PHP_QUERY_RFC3986):'');
    }

    /** Costruisce archivio paginato dalle pagine pubblicate. */
    function merkury_cms_render_page_cards(array $s, string $categoryFilter, string $monthFilter='', ?int &$total=null): ?string {
        global $pdo;
        if(!cms_table_exists('pages')) return null;
        $limit=max(1,min(30,(int)$s['posts_per_page']));
        $where=["p.status='published'",'p.deleted_at IS NULL']; $params=[];
        if($categoryFilter!=='' && cms_table_exists('page_categories') && cms_table_exists('categories')) {
            $where[]='EXISTS (SELECT 1 FROM '.cms_table('page_categories').' pc INNER JOIN '.cms_table('categories').' c ON c.id=pc.category_id WHERE pc.page_id=p.id AND c.slug=?)';
            $params[]=$categoryFilter;
        }
        if($monthFilter!=='' && preg_match('/^\\d{4}-(0[1-9]|1[0-2])$/',$monthFilter)) {
            $where[]="DATE_FORMAT(COALESCE(p.published_at,p.updated_at),'%Y-%m')=?"; $params[]=$monthFilter;
        }
        $whereSql=implode(' AND ',$where);
        $count=$pdo->prepare('SELECT COUNT(*) FROM '.cms_table('pages').' p WHERE '.$whereSql); $count->execute($params);
        $total=(int)$count->fetchColumn();
        $pagesCount=max(1,(int)ceil($total/$limit));
        $current=max(1,min($pagesCount,(int)($_GET['mk_page']??1))); $offset=($current-1)*$limit;
        $q=$pdo->prepare('SELECT p.id,p.title,p.slug,p.excerpt,p.content,p.background_image,p.published_at FROM '.cms_table('pages').' p WHERE '.$whereSql.' ORDER BY COALESCE(p.published_at,p.updated_at) DESC,p.id DESC LIMIT '.$limit.' OFFSET '.$offset);
        $q->execute($params); $pages=$q->fetchAll();
        $out='';
        foreach($pages as $page) {
            $title=(string)$page['title']; $url='index.php?page='.rawurlencode((string)$page['slug']);
            $summary=trim((string)($page['excerpt']??''));
            if($summary==='') {
                $summary=trim(preg_replace('/\\s+/u',' ',html_entity_decode(strip_tags((string)($page['content']??'')),ENT_QUOTES|ENT_HTML5,'UTF-8'))??'');
                $summary=function_exists('mb_substr')?mb_substr($summary,0,230):substr($summary,0,230);
                if($summary!=='') $summary=rtrim($summary," \\t\\n\\r\\0\\x0B.,;:").'…';
            }
            $image=trim((string)($page['background_image']??''));
            if(!preg_match('~^(uploads/images/[A-Za-z0-9._/-]+|https?://[^\\s\"]+)$~i',$image)) $image='';
            $out.='<article class="merkury-post-card'.($image===''?' no-image':' has-image').'">';
            if($image!=='') $out.='<div class="merkury-card-image"><img src="'.merkury_cms_h(merkury_cms_asset_url($image)).'" alt=""></div>';
            $out.='<div class="merkury-card-body"><h3><a href="'.merkury_cms_h($url).'">'.merkury_cms_h($title).'</a></h3>';
            if($summary!=='') $out.='<p>'.merkury_cms_h($summary).'</p>';
            $out.='<a class="read-more" href="'.merkury_cms_h($url).'">Continua a leggere →</a></div></article>';
        }
        if($total===0) $out='<p class="merkury-empty-archive">Non ci sono articoli pubblicati per il periodo selezionato.</p>';
        if($pagesCount>1) {
            $nav='<nav class="merkury-pagination" aria-label="Paginazione articoli">';
            if($current>1) $nav.='<a class="mk-page-link" href="'.merkury_cms_h(merkury_cms_archive_url($current-1,$monthFilter,$categoryFilter)).'">← Precedente</a>';
            $windowStart=max(1,$current-2); $windowEnd=min($pagesCount,$current+2);
            if($windowStart>1) $nav.='<a class="mk-page-link" href="'.merkury_cms_h(merkury_cms_archive_url(1,$monthFilter,$categoryFilter)).'">1</a>';
            if($windowStart>2) $nav.='<span class="mk-page-ellipsis">…</span>';
            for($i=$windowStart;$i<=$windowEnd;$i++) $nav.=$i===$current?'<span class="mk-page-link current" aria-current="page">'.$i.'</span>':'<a class="mk-page-link" href="'.merkury_cms_h(merkury_cms_archive_url($i,$monthFilter,$categoryFilter)).'">'.$i.'</a>';
            if($windowEnd<$pagesCount-1) $nav.='<span class="mk-page-ellipsis">…</span>';
            if($windowEnd<$pagesCount) $nav.='<a class="mk-page-link" href="'.merkury_cms_h(merkury_cms_archive_url($pagesCount,$monthFilter,$categoryFilter)).'">'.$pagesCount.'</a>';
            if($current<$pagesCount) $nav.='<a class="mk-page-link" href="'.merkury_cms_h(merkury_cms_archive_url($current+1,$monthFilter,$categoryFilter)).'">Successivo →</a>';
            $nav.='</nav>'; $out=$nav.$out.$nav;
        }
        return $out;
    }

    function merkury_cms_month_archive(): string {
        global $pdo;
        if(!cms_table_exists('pages')) return '';
        $sql="SELECT DATE_FORMAT(COALESCE(published_at,updated_at),'%Y-%m') AS ym, COUNT(*) AS total FROM ".cms_table('pages')." WHERE status='published' AND deleted_at IS NULL GROUP BY ym ORDER BY ym DESC";
        $rows=$pdo->query($sql)->fetchAll(); if(!$rows) return '';
        $months=['01'=>'Gennaio','02'=>'Febbraio','03'=>'Marzo','04'=>'Aprile','05'=>'Maggio','06'=>'Giugno','07'=>'Luglio','08'=>'Agosto','09'=>'Settembre','10'=>'Ottobre','11'=>'Novembre','12'=>'Dicembre'];
        $active=(string)($_GET['mk_month']??''); $html='<section class="merkury-month-archive"><h2>Archivio</h2><ul><li><a href="index.php">Tutti gli articoli</a></li>';
        foreach($rows as $row) { $ym=(string)$row['ym']; if(!preg_match('/^(\\d{4})-(\\d{2})$/',$ym,$m)) continue; $label=$months[$m[2]].' '.$m[1]; $html.='<li><a'.($active===$ym?' class="active"':'').' href="'.merkury_cms_h(merkury_cms_archive_url(1,$ym)).'">'.merkury_cms_h($label).' <span>('.(int)$row['total'].')</span></a></li>'; }
        return $html.'</ul></section>';
    }

    /** Navigazione cronologica tra pagine pubblicate. */
    function merkury_cms_single_navigation(): string {
        global $pdo;
        $slug=trim((string)($_GET['page']??'')); if($slug==='' || !cms_table_exists('pages')) return '';
        $q=$pdo->prepare("SELECT id,slug FROM ".cms_table('pages')." WHERE status='published' AND deleted_at IS NULL ORDER BY COALESCE(published_at,updated_at) DESC,id DESC");
        $q->execute(); $rows=$q->fetchAll(); $index=null;
        foreach($rows as $i=>$row) if((string)$row['slug']===$slug) {$index=$i;break;}
        if($index===null) return '';
        $html='<nav class="merkury-article-navigation" aria-label="Navigazione articoli">';
        if(isset($rows[$index+1])) $html.='<a class="mk-article-prev" href="index.php?page='.rawurlencode((string)$rows[$index+1]['slug']).'">← Articolo precedente</a>';
        $html.='<a class="mk-article-home" href="index.php">⌂ Home — ultimi articoli</a>';
        if($index>0) $html.='<a class="mk-article-next" href="index.php?page='.rawurlencode((string)$rows[$index-1]['slug']).'">Articolo successivo →</a>';
        return $html.'</nav>';
    }

    function merkury_cms_public_buffer(string $html): string {
        global $pdo;
        if(stripos($html,'<body')===false || stripos($html,'site-header')===false || stripos($html,'site-footer')===false) return $html;
        $s=merkury_cms_settings(); $esc='merkury_cms_h';
        $headerBlock=merkury_cms_extract_element($html,'header');
        $header=$headerBlock['inner']??'';
        preg_match('~<nav\b[^>]*>(.*?)</nav>~is',$header,$navBlock);
        $navHtml=$navBlock[1]??$header;
        preg_match_all('~<a\b[^>]*href="([^"]*)"[^>]*>(.*?)</a>~is',$navHtml,$navs,PREG_SET_ORDER);
        $nav='';
        foreach($navs as $n) {
            $href=$n[1]; $label=trim(strip_tags(html_entity_decode($n[2],ENT_QUOTES|ENT_HTML5,'UTF-8')));
            if($label==='') continue;
            $slug=''; if(preg_match('/[?&]category=([^&]+)/',$href,$sm)) $slug=rawurldecode($sm[1]);
            $meta=is_array($s['category_meta']??null)?($s['category_meta'][$slug]??[]):[];
            $color=is_array($meta)&&preg_match('/^#[0-9a-fA-F]{6}$/',(string)($meta['color']??''))?$meta['color']:'';
            $icon=is_array($meta)?trim((string)($meta['icon']??'')):'';
            $iconImage=is_array($meta)?trim((string)($meta['icon_image']??'')):'';
            $style=$color!==''?' style="color:'.$esc($color).'"':'';
            $iconHtml=$iconImage!==''?'<img class="merkury-category-icon-image" src="'.$esc(merkury_cms_asset_url($iconImage)).'" alt="">':($icon!==''?'<span class="merkury-category-icon">'.$esc($icon).' </span>':'');
            $nav.='<a href="'.$esc($href).'"'.$style.'>'.$iconHtml.$esc($label).'</a>';
        }
        if($nav==='') $nav='<a href="index.php">Home</a>';

        $mainBlock=merkury_cms_extract_element($html,'main');
        $main=$mainBlock['inner']??'';
        $categoryFilter=trim((string)($_GET['category']??''));
        // Verifica la stessa destinazione nativa risolta dal front-end CMS.
        // Non si basa su variabili locali del controller CMS: interroga le chiavi
        // native e considera valida solo una pagina effettivamente pubblicata.
        $landingPageId=0;
        if ($categoryFilter!=='' && cms_table_exists('categories')) {
            $landingQuery=$pdo->prepare('SELECT landing_page_id FROM '.cms_table('categories').' WHERE slug=? LIMIT 1');
            $landingQuery->execute([$categoryFilter]);
            $landingPageId=(int)($landingQuery->fetchColumn()?:0);
        } elseif ($categoryFilter==='') {
            $landingPageId=(int)cms_setting('home_page_id','0');
        }
        if ($landingPageId>0 && cms_table_exists('pages')) {
            $validLanding=$pdo->prepare("SELECT COUNT(*) FROM ".cms_table('pages')." WHERE id=? AND status='published' AND deleted_at IS NULL");
            $validLanding->execute([$landingPageId]);
            if ((int)$validLanding->fetchColumn()===0) $landingPageId=0;
        }
        $isArchiveRequest=!isset($_GET['page']) && $landingPageId===0;
        $hero='';
        if($isArchiveRequest && !empty($s['show_intro'])) {
            $image=trim((string)$s['hero_image']);
            $imageUrl=$image!==''?merkury_cms_asset_url($image):'';
            $style=' style="--merkury-overlay:'.(max(0,min(85,(int)$s['hero_overlay']))/100).';'.($imageUrl!==''?'--merkury-hero-image:url(\''.$esc($imageUrl).'\')':'').'"';
            $hero='<section class="merkury-hero '.($imageUrl!==''?'has-image':'no-image').'"'.$style.'><div class="merkury-hero-inner">'.(trim($s['intro_eyebrow'])!==''?'<p class="merkury-hero-eyebrow">'.$esc($s['intro_eyebrow']).'</p>':'').(trim($s['intro_title'])!==''?'<h1>'.$esc($s['intro_title']).'</h1>':'').(trim($s['intro_text'])!==''?'<p>'.nl2br($esc($s['intro_text'])).'</p>':'').'</div></section>';
        }

        if($isArchiveRequest) {
            // L'archivio cronologico si applica soltanto quando il CMS non ha risolto una pagina assegnata.
            $main=$hero;
            $total=0; $cards=merkury_cms_render_page_cards($s,$categoryFilter,(string)($_GET['mk_month']??''),$total);
            $heading='';
            if(!empty($_GET['mk_month']) && preg_match('/^(\\d{4})-(\\d{2})$/',(string)$_GET['mk_month'],$mm)) {
                $months=['01'=>'Gennaio','02'=>'Febbraio','03'=>'Marzo','04'=>'Aprile','05'=>'Maggio','06'=>'Giugno','07'=>'Luglio','08'=>'Agosto','09'=>'Settembre','10'=>'Ottobre','11'=>'Novembre','12'=>'Dicembre'];
                $heading='<h2>'. $esc($months[$mm[2]].' '.$mm[1]).'</h2>';
            }
            $main.='<section class="page-list"><h2>'.($heading!==''?'Archivio':'Ultimi articoli').'</h2>'.$heading.'<div class="page-grid merkury-post-grid '.($s['preset']==='photographic'?'photographic':'chronological').'">'.($cards??'').'</div></section>';
            $layout='';
            if(!empty($s['show_sidebar'])) {
                $layout='<aside class="merkury-sidebar"><section><h2>Categorie</h2><ul>';
                foreach($navs as $n) {
                    $label=trim(strip_tags($n[2])); if($label==='' || strcasecmp($label,'Home')===0) continue;
                    $slug=''; if(preg_match('/[?&]category=([^&]+)/',$n[1],$sm)) $slug=rawurldecode($sm[1]);
                    $meta=is_array($s['category_meta']??null)?($s['category_meta'][$slug]??[]):[];
                    $color=is_array($meta)&&preg_match('/^#[0-9a-fA-F]{6}$/',(string)($meta['color']??''))?$meta['color']:'';
                    $icon=is_array($meta)?trim((string)($meta['icon']??'')):''; $iconImage=is_array($meta)?trim((string)($meta['icon_image']??'')):'';
                    $style=$color!==''?' style="color:'.$esc($color).'"':'';
                    $iconHtml=$iconImage!==''?'<img class="merkury-category-icon-image" src="'.$esc(merkury_cms_asset_url($iconImage)).'" alt="">':($icon!==''?'<span class="merkury-category-icon">'.$esc($icon).' </span>':'');
                    $layout.='<li><a href="'.$esc($n[1]).'"'.$style.'>'.$iconHtml.$esc($label).'</a></li>';
                }
                $layout.='</ul></section>'.merkury_cms_month_archive().'</aside>';
            }
            $main='<div class="merkury-home-layout"><div class="merkury-archive-main">'.$main.'</div>'.$layout.'</div>';
        } else {
            $main=preg_replace('~<article class="site-panel page-panel"~','<article class="site-panel page-panel merkury-single"',$main,1)??$main;
            $singleNav=merkury_cms_single_navigation();
            if($singleNav!=='') $main=preg_replace('~</article>~i',$singleNav.'</article>',$main,1)??$main;
        }

        $title=$esc((string)($s['site_name']??cms_setting('site_name','MercurioCMS')));
        $logo=trim((string)($s['site_logo']??cms_setting('site_logo','')));
        $logoUrl=$logo!==''?merkury_cms_asset_url($logo):'';
        $markUrl=!empty($s['brand_mark_image'])?merkury_cms_asset_url((string)$s['brand_mark_image']):'';
        $brand=($logoUrl!==''?'<img src="'.$esc($logoUrl).'" alt="">':'<span class="merkury-mark">'.($markUrl!==''?'<img src="'.$esc($markUrl).'" alt="">':'M').'</span>').'<span>'.$title.'</span>';
        $head='<header class="merkury-site-header"><div class="merkury-header-inner"><a class="merkury-brand" href="index.php">'.$brand.'</a><nav class="merkury-nav-wrap" aria-label="Navigazione principale">'.$nav.'</nav></div></header>';
        $footer='<footer class="merkury-site-footer"><div class="merkury-footer-inner"><strong>'.$title.'</strong><span class="merkury-footer-note">'.$esc($s['footer_text']).'</span><button class="merkury-mode-toggle" type="button" aria-pressed="false">☾ <span>Modalità scura</span></button>'.(!empty($s['show_credits'])?'<small class="merkury-credits">Merkury 1.0.3</small>':'').'</div></footer>';
        $base=merkury_cms_url('assets/css/merkury-cms.css'); $js=merkury_cms_url('assets/js/color-mode.js');
        $nc=is_array($s['native_colors']??null)?$s['native_colors']:[];
        $cssVars='--merkury-primary:'.$esc($s['primary_color']).';--merkury-accent:'.$esc($s['accent_color']).';--merkury-ink:'.$esc($s['ink_color']).';--merkury-paper:'.$esc($s['paper_color']).';--merkury-chrome-bg:'.$esc($s['chrome_color']).';--merkury-chrome-text:'.$esc($nc['color_header_text']??'#FFFFFF').';--merkury-card:'.$esc($nc['color_surface']??'#FFFFFF').';--merkury-hero-height:'.max(240,min(900,(int)$s['hero_height'])).'px;--merkury-hero-fit:'.($s['hero_fit']==='contain'?'contain':'cover').';--merkury-overlay:'.(max(0,min(85,(int)$s['hero_overlay']))/100).';--primary:'.$esc($s['primary_color']).';--site-bg:'.$esc($s['paper_color']).';--surface:'.$esc($nc['color_surface']??'#FFFFFF').';--site-text:'.$esc($s['ink_color']).';--footer-bg:'.$esc($nc['color_footer_bg']??$s['chrome_color']).';--footer-text:'.$esc($nc['color_footer_text']??'#FFFFFF').';--header-bg:'.$esc($s['chrome_color']).';--header-text:'.$esc($nc['color_header_text']??'#FFFFFF').';--hero-bg:'.$esc($nc['color_hero_bg']??$s['paper_color']).';--hero-eyebrow:'.$esc($s['accent_color']).';--hero-title:'.$esc($nc['color_hero_title']??$s['primary_color']).';--hero-text:'.$esc($nc['color_hero_text']??$s['ink_color']).';';
        if($headerBlock) $html=str_replace($headerBlock['full'],$head,$html);
        // La favicon è una risorsa condivisa: usa sempre e soltanto il valore
        // nativo di MercurioCMS, eliminando eventuali riferimenti duplicati o obsoleti.
        $favicon=trim((string)cms_setting('site_favicon',''));
        $html=preg_replace('~<link\b(?=[^>]*\brel\s*=\s*["\'](?:shortcut\s+)?icon["\'])[^>]*>~i','',$html)??$html;
        if($favicon!=='') {
            $faviconUrl=merkury_cms_asset_url($favicon);
            $faviconDiskPath=dirname(__DIR__,2).'/'.ltrim($favicon,'/');
            $faviconVersion=is_file($faviconDiskPath)?(string)filemtime($faviconDiskPath):(string)time();
            $faviconUrl.=(str_contains($faviconUrl,'?')?'&':'?').'v='.rawurlencode($faviconVersion);
            $fav='<link rel="icon" href="'.$esc($faviconUrl).'">';
            if(preg_match('~</head>~i',$html)) $html=preg_replace('~</head>~i',$fav.'</head>',$html,1)??$html;
        }
        if($mainBlock) $html=str_replace($mainBlock['full'],'<main class="merkury-main"><div class="site-shell">'.$main.'</div></main>',$html);
        $footerBlock=merkury_cms_extract_element($html,'footer'); if($footerBlock) $html=str_replace($footerBlock['full'],$footer,$html);
        $html=preg_replace('~<link rel="stylesheet" href="assets/css/public\.css">~','<link rel="stylesheet" href="'.$base.'"><style>:root{'.$cssVars.'}.merkury-hero{background-color:var(--hero-bg)}.merkury-hero h1{color:var(--hero-title)}.merkury-hero p{color:var(--hero-text)}.merkury-hero .merkury-hero-eyebrow{color:var(--hero-eyebrow)}</style>',$html,1)??$html;

        $previewScript='';
        if(isset($_GET['merkury_preview']) && !empty($_SESSION['admin_id']) && is_string($_GET['merkury_preview']) && hash_equals(csrf_token(),$_GET['merkury_preview'])) {
            $previewScript='<script>(function(){window.addEventListener("message",function(e){if(e.origin!==window.location.origin||!e.data||e.data.type!=="merkury-live-preview")return;var s=e.data.settings||{},r=document.documentElement,b=document.body;var map={primary_color:"--merkury-primary",accent_color:"--merkury-accent",ink_color:"--merkury-ink",paper_color:"--merkury-paper",chrome_color:"--merkury-chrome-bg"};Object.keys(map).forEach(function(k){if(/^#[0-9a-fA-F]{6}$/.test(s[k]||"")){r.style.setProperty(map[k],s[k]);b.style.setProperty(map[k],s[k])}});var h=document.querySelector(".merkury-hero");if(!h&&s.show_intro){h=document.createElement("section");h.className="merkury-hero no-image";var inner=document.createElement("div");inner.className="merkury-hero-inner";h.appendChild(inner);var shell=document.querySelector(".merkury-main .site-shell");if(shell)shell.insertBefore(h,shell.firstChild)}if(h){var inner=h.querySelector(".merkury-hero-inner");inner.innerHTML="";if(s.intro_title){var t=document.createElement("h1");t.textContent=s.intro_title;inner.appendChild(t)}if(s.intro_text){var p=document.createElement("p");p.textContent=s.intro_text;inner.appendChild(p)}h.style.display=s.show_intro?"flex":"none";if(s.hero_image_url){h.classList.add("has-image");h.classList.remove("no-image");h.style.setProperty("--merkury-hero-image","url(\""+s.hero_image_url.replace(/[\"()]/g,"")+"\")")}else{h.classList.remove("has-image");h.classList.add("no-image");h.style.removeProperty("--merkury-hero-image")}if(s.hero_height)h.style.minHeight=Math.max(240,Math.min(900,Number(s.hero_height)))+"px";if(s.hero_overlay!==undefined)h.style.setProperty("--merkury-overlay",Math.max(0,Math.min(85,Number(s.hero_overlay)))/100);if(s.hero_fit)h.style.setProperty("--merkury-hero-fit",s.hero_fit==="contain"?"contain":"cover")}var hd=document.querySelector(".merkury-header-inner");if(hd&&s.header_style)hd.style.minHeight=s.header_style==="compact"?"64px":"132px";document.querySelectorAll(".merkury-post-grid").forEach(function(g){g.classList.toggle("photographic",s.preset==="photographic");g.classList.toggle("chronological",s.preset!=="photographic")});if(s.color_mode){var mode=s.color_mode;if(mode==="system")mode=window.matchMedia&&window.matchMedia("(prefers-color-scheme: dark)").matches?"dark":"light";r.setAttribute("data-merkury-mode",mode);b.setAttribute("data-merkury-mode",mode)}var f=document.querySelector(".merkury-site-footer");if(f&&s.footer_text!==undefined){var n=f.querySelector(".merkury-footer-note");if(n)n.textContent=s.footer_text}});parent.postMessage({type:"merkury-live-preview-ready"},window.location.origin)})();</script>';
        }
        $html=preg_replace('~</body>~',$previewScript.'<script src="'.$js.'" defer></script></body>',$html,1)??$html;
        $mode=in_array($s['color_mode'],['light','dark'],true)?$s['color_mode']:'system';
        $preset=$s['preset']==='photographic'?'photographic':'chronological';
        $compact=$s['header_style']==='compact'?' merkury-compact-header':'';
        $html=preg_replace_callback('~<body\b([^>]*)>~i',static function($m)use($preset,$mode,$cssVars,$compact){
            $attrs=preg_replace('~\s+(?:class|style)=("|\').*?\1~is','',(string)$m[1]);
            return '<body'.$attrs.' class="merkury-body merkury-preset-'.$preset.' merkury-mode-'.$mode.$compact.'" style="'.$cssVars.'">';
        },$html,1)??$html;
        return $html;
    }
    if(PHP_SAPI!=='cli' && basename($_SERVER['SCRIPT_NAME']??'')==='index.php') ob_start('merkury_cms_public_buffer');
}
