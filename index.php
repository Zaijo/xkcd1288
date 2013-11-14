<?php
$default_url = "http://xkcd.com/1288/";
$url = isset($_GET['url']) ? $_GET['url'] : $default_url;

// Get the hostname
$matches;
preg_match('@^(https?://[^#/]+)@', $url, $matches);
$hostname = $matches[0];

// Retrieve the website source
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, 0);
$html =  curl_exec($ch);
curl_close($ch);

// Prepare substitution
$replace = array(
        "witnesses" => "these dudes I know",
        "allegedly" => "kinda probably",
        "new study" => "tumblr post",
        "rebuild" => "avenge",
        "space" => "spaaace",
        "google glass" => "virtual boy",
        "smartphone" => "pokédex",
        "electric" => "atomic",
        "senator" => "elf-lord",
        "car" => "cat",
        "election" => "eating contest",
        "congressional leaders" => "river spirits",
        "homeland security" => "homestar runner",
        "could not be reached for comment" => "is guilty and everyone knows it",
    );

$a = array();
$b = $replace;

// Preprocess substitution arrays
foreach($replace as $th => $that) {
	$a[] = "@$th@i";
}
$replacements_made = 0;

// Substitute and show
$early = preg_replace($a, $b, $html, -1, $replacements_made);

// Fix some rooted relative links
$early = preg_replace('@"/@', '"'.$hostname.'/', $early);

// Control panel
ob_start();
include "control_panel.php";
$control_panel_html = ob_get_contents();
ob_end_clean();

// Add control panel to <body>
$early = preg_replace('@<body(.*)>@', "<body\1>$control_panel_html", $early);

// Add control panel styles before </head>
$control_panel_styles = file_get_contents("style.html");
$early = preg_replace('@</head>@', "$control_panel_styles</head>", $early);

// Add (<number of replaces>) to title
echo preg_replace('/<title.*>(.*)<\/title>/', '<title>('.$replacements_made.') \1</title>', $early);

?> 
