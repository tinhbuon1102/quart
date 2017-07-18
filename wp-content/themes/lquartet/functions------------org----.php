<?php
//アイキャッチ画像
add_theme_support('post-thumbnails');
add_image_size( 'large_thumbnail', 200, 250, true );
//ウイジェット
register_sidebar();
//WordPressのバージョン情報の出力を停止
remove_action('wp_head','wp_generator');
//RSSフィードの情報を出力
add_theme_support('automatic-feed-links');
//管理バーを非表示
add_filter( 'show_admin_bar', '__return_false' );
// メインコンテンツの幅を指定
if ( ! isset( $content_width ) ) $content_width = 600;

// RSS2 の feed リンクを出力
add_theme_support( 'automatic-feed-links' );

// カスタムメニューを有効化
add_theme_support( 'menus' );

// カスタムメニューの「場所」を設定
register_nav_menu( 'header-navi', 'ヘッダーのナビゲーション' );
register_nav_menu( 'footer-navi', 'フッターのナビゲーション' );


// サイドバーウィジットを有効化
register_sidebar( array(
	'name' => 'サイドバーウィジット-1',
	'id' => 'sidebar-1',
	'description' => 'サイドバーのウィジットエリアです。',
    'before_widget' => '<div id="%1$s" class="widget %2$s">',
    'after_widget' => '</div>',
) );
?>