<?php
return array (
  'LANG_PATH' => './App/Lang/zh-cn/Admin/',
  'DEFAULT_LANG' => 'zh-cn',
  'TOKEN_ON' => false,
  'uc_status' => '0',
  'default_theme' => 'mogujie',
  'url_model' => '0',
  'url_rewrite' => '0',
  'url_html' => '0',
  'html_url_suffix' => '.html',
  'html_file_suffix' => '.html',
  'url_rewrite_article' => 'article',
  'url_rewrite_search' => 'search',
  'url_rewrite_shop' => 'shop',
  'url_rewrite_album' => 'album',
  'url_rewrite_cate' => 'cate',
  'url_rewrite_tag' => 'tag',
  'url_rewrite_item' => 'item',
  'url_rewrite_user' => 'u',
  'url_dir_shop' => 'shop',
  'url_dir_albumCate' => 'albumCate',
  'url_dir_album' => 'album',
  'url_dir_cate' => 'cates',
  'url_dir_tag' => 'tags',
  'url_dir_item' => 'items',
  'url_dir_articleCate' => 'articleCate',
  'url_dir_article' => 'article',
  'html_cache_on' => '0',
  'html_cache_index' => 1,
  'html_cache_cate' => 1,
  'html_cache_album' => 1,
  'html_cache_rules' => 
  array (
    'index:' => 
    array (
      0 => '{:module}_{:action}/{$_SERVER.REQUEST_URI|md5}',
      1 => 3600,
    ),
    'cate:' => 
    array (
      0 => '{:module}_{:action}/{$_SERVER.REQUEST_URI|md5}',
      1 => 3600,
    ),
    'album:' => 
    array (
      0 => '{:module}_{:action}/{$_SERVER.REQUEST_URI|md5}',
      1 => 3600,
    ),
  ),
);
?>