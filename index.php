<?php require_once "data.builder/data.php"; ?>
<?php $is_toc  = !count($_GET["chap"]); ?>
<?php $curr_call = (!$is_toc ? $_GET["chap"] : "TOC"); ?>
<?php if(!$is_toc && ($curr_call < 1 || $curr_call > end($book_content)["chap"])) { header("location: /"); exit; } ?>
<?php $is_last_chap = ($curr_call == end($book_content)["chap"]) ?>
<?php $prev_call  = (!$is_toc ? $_GET["chap"] - 1 : "TOC"); ?>
<?php $next_call  = $curr_call + 1; ?>
<?php if(!$is_toc) : ?>
    <?php $curr_chap = $book_content[$curr_call - 1]; ?>
    <?php $prev_chap = $book_content[$prev_call - 1]; ?>
    <?php $next_chap = $book_content[$next_call -  1]; ?>
<?php endif; ?>
<?php $get_chap_link = function($no) { if($no == "TOC" || $no < 1) return "/"; return "/?chap=$no"; }; ?>
<?php $get_chap_toc = function($chap) { return $chap["chap"].". ".$chap["title"]; }; ?>
<?php $render_nav_links = function() use($get_chap_link, $prev_call, $get_chap_toc, $prev_chap, $curr_chap, $next_call, $next_chap, $is_last_chap) { ?>
    <div class="row" >
        <div class="col-md-4 text-left">
            <a href="<?php echo $get_chap_link($prev_call) ?>">
               &laquo; <?php echo ($prev_call == "TOC" ? "Table Of Content" : $get_chap_toc($prev_chap)) ?>
            </a>
        </div>
        <div class="col-md-4 text-center"><?php echo $curr_chap["title"] ?></div>
        <div class="col-md-4 text-right">
            <?php if(!$is_last_chap): ?>
            <a href="<?php echo $get_chap_link($next_call) ?>">
                <?php echo $get_chap_toc($next_chap) ?> &raquo;
            </a>
            <?php endif; ?>
        </div>
    </div>
<?php }; ?>
<html>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <head>
        <link rel="stylesheet" href="/tb/css/bootstrap.min.css">
        <link rel="stylesheet" href="/tb/css/bootstrap-theme.min.css">
        <script src="/tb/js/bootstrap.min.js"></script>
        <style id="reader-render-style">
            *{font-size: 20px; -webkit-font-smoothing: antialiased;}
            *, .normal-font { font-family:'Garamond','527df14f852458fb770b56af0020001','Garamond'; }
            .italic-font { font-family:'Garamond Italic','527df14f852458fb770b56af0010001','Garamond Italic'; font-style:italic; } 
            <?php if($is_toc) : ?>
            #toc { margin: 10px; padding:10px; }
            #toc .toc-chap { }
            #toc .toc-chap-no { display: inline }
            #toc a:hover {text-decoration: none; color: #004488; font-style: italic }
            <?php else: ?>
            #content { padding: 30px; }
            #content #sections { margin:80px 10px; text-align: justify; }
            #sections .section {margin-bottom: 40px; }
            #sections .section .section-no { font-weight: bold }
            #sections .section .section-content { display: inline }
            <?php endif;  ?>
        </style>
    </head>
    <body>
        <div class="container">
            <h1 class='italic-font text-center' style="font-variant: small-caps; "><?php echo $book_title ?></h1>
            <h2 class="italic-font text-center" style="font-size: 20px;"><?php echo $book_author ?></h2>
            <div style="border-bottom: 2px dotted #e6e6e6;height: 20px;"></div>
        <?php
            if(!count($_GET["chap"])):
        ?>
            <h3>Table Of Contents</h3>
            <div id="toc">
            <?php
                foreach ($book_content as $chap):
            ?>
                <div id="toc-chap-<?php echo $chap["chap"] ?>" class="toc-chap">
                    <a href="<?php echo $get_chap_link($chap["chap"]) ?>"><?php echo $get_chap_toc($chap) ?></a>
                </div>
            <?php
                endforeach;
            ?>
            </div>
        <?php
            else:
        ?>
            <div id="content">
                <div id="header">
                    <div class="row"><div class="col-md-12 text-center" style="margin-bottom: 20px"><a href="/">Table Of Content</a></div></div>
                    <?php $render_nav_links() ?>
                </div>
                <div id="sections">
                    <?php foreach ($curr_chap["sections"] as $section) :  ?>
                    <div class="section" id="section-<?php echo $section["sec"]; ?>">
                        <span class="section-no"><?php echo $section["sec"] ?></span>.&nbsp;<p class="section-content"><?php echo iconv("Windows-1252", "UTF-8", $section["content"])?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div id="footer">
                    <?php $render_nav_links() ?>
                    <div class="row"><div class="col-md-12 text-center" style="margin-top: 20px"><a href="/">Table Of Content</a></div></div>
                </div>
            </div>
        <?php
            endif;
        ?>
            <div style="border-bottom: 2px dotted #e6e6e6;height: 20px;"></div>
            <h2 class="italic-font text-center" style="font-size: 20px;"><?php echo $book_author ?></h2>
            <h1 class='italic-font text-center' style="font-variant: small-caps; "><?php echo $book_title ?></h1>
        </div>
    </body>
</html>