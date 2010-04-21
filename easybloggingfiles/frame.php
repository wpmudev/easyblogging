<?php
//Sift the querystring value through a switch statement to make sure there's no security holes
switch ($_GET['frame']) {
    case 'edit':
        $page = 'edit';
        $frame_url = admin_url('edit.php');
    break;
    case 'page-new':
        $page = 'page-new';
        $frame_url = admin_url('page-new.php');
    break;
    case 'edit-pages':
        $page = 'edit-pages';
        $frame_url = admin_url('edit-pages.php');
    break;
    case 'themes':
        $page = 'themes';
        $frame_url = admin_url('themes.php');
    break;
    case 'widgets':
        $page = 'widgets';
        $frame_url = admin_url('widgets.php');
    break;
    case 'edit-comments':
        $page = 'edit-comments';
        $frame_url = admin_url('edit-comments.php');
    break;
    case 'premium-themes':
        $page = 'themes';
        $frame_url = admin_url('themes.php') . '?page=premium-themes';
    break;
    case 'supporter':
        $page = 'supporter';
        $frame_url = admin_url('supporter.php');
    break;
    case 'premium-support':
        $page = 'supporter';
        $frame_url = admin_url('supporter.php') . '?page=premium-support';
    break;
    case 'profile':
        $page = 'profile';
        $frame_url = admin_url('profile.php');
    break;
    case 'noteasy':
        $page = 'noteasy';
        break;
    case 'post-new':
    default:
        $frame_url = apply_filters('easy_admin_more_tabs_url',$_GET['frame']);
        $page = apply_filters('easy_admin_more_tabs_page',$_GET['frame']);
        
        //Added $frame_url == $_GET['frame'] because apply_filters automatically returns $_GET['frame'] by default. That stopps $frame_url from being empty, so we had to add the extra statement
        if (empty($frame_url) || $frame_url == $_GET['frame']) $frame_url = admin_url('post-new.php');
        if (empty($page)) $page = 'post-new';
        break;
}

if (strpos($frame_url, '?') !== false) {
    $has_querystring = true;
}

if ($page != 'noteasy') {
?>
<html>
<head>
<script type="text/javascript">
jQuery(document).ready(function(){
    /*###Commented out, do we need this?
    var querystring = '';
    var anchor = jQuery(document).attr('location').hash; // the anchor in the URL
    if (anchor.indexOf('|') > -1) {
        querystring = anchor.substring(anchor.indexOf('|')+1); //+1 to ignore the |
        anchor = anchor.substring(0,anchor.indexOf('|'));
    }*/
    
    //Do we need to pass in the querystring?
    if (querystring != '') {
        jQuery('iframe#<?php echo $page?>-php').attr('src','<?php echo $frame_url; if ($has_querystring) echo '&'; else echo '?';?>' + querystring);
    }
});
</script>
</head>
<body>
<iframe id="<?php echo $page?>-php" scrolling="no" src="<?php echo $frame_url; ?>" style="height: 600px; width: 100%; border: none;" frameborder="0"></iframe>
</body>
</html>
<?php } else {
    require_once('noteasy.php');
}
?>