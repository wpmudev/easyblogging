<html>
<head>
</head>
<body>
<?php
//Sift the querystring value through a switch statement to make sure there's no security holes
switch ($_GET['frame']) {
    case 'edit':
        $page = 'edit';
    break;
    case 'page-new':
        $page = 'page-new';
    break;
    case 'edit-pages':
        $page = 'edit-pages';
    break;
    case 'themes':
        $page = 'themes';
    break;
    case 'widgets':
        $page = 'widgets';
    break;
    case 'edit-comments':
        $page = 'edit-comments';
    break;
    case 'post-new':
        $page = 'post-new';
    default:
        $page = apply_filters('easy_admin_more_tabs_frame',$_GET['frame']);
        if (!$page) $page = 'post-new';
        break;
}
?>
<iframe id="<?=$page?>-php" scrolling="no" src="<?=admin_url($page.'.php'); ?>" style="height: 600px; width: 100%; border: none;" frameborder="0"></iframe>
</body>
</html>
