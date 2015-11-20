<?php
// Get composer
require_once(dirname( __FILE__ )."/../../../../../vendor/autoload.php");

// Attempt to load config
$config = dirname( __FILE__ ) .'/../../../../../';
if (!file_exists($config.'/.env')) die("No config (.env) found :(");

Dotenv::load($config);
Dotenv::required(array('WP_SITEURL'));
$blog_url = parse_url(getenv('WP_SITEURL'));

// Fake some server vars to make WP happy - use .env to make this portable
$_SERVER = array(
"HTTP_HOST" => $blog_url['host'],
"SERVER_NAME" => "http://".$blog_url['host'],
"REQUEST_URI" => isset($blog_url['path']) ? $blog_url['path'] : '',
"REQUEST_METHOD" => "GET"
);

// Boot wp (Shortinit set)
require_once( dirname( __FILE__ ) . '/../../../../wp/wp-load.php' );



$sources = array(
	'BBC' => array('bbc','bbc.co.uk'),
	'The Observer' => array('observer','theguardian.com/observer'),
	'The Guardian' => array('guardian','theguardian.com'),
	'The Times' => array('times','thetimes.co.uk'),
	'The Sun' => array('sun','thesun.co.uk'),
	'The London Evening Standard' => array('standard','standard.co.uk'),
	'The Daily Star' => array('dailystar','dailystar.co.uk'),
	'The Daily Mirror' =>array('dailymirror','mirror.co.uk'),
	'The Daily Mail' => array('dailymail','dailymail.co.uk'),
	'The Daily Express' => array('dailyexpress','express.co.uk'),
	'Huffington Post' => array('huffingtonpost','huffingtonpost.co.uk'),
	'The Scotsman' => array('scotsman','scotsman.com'),
	'The Australian' => array('theaustralian','theaustralian.com.au'),
	'Daily Post' => array('dailypost','dailypost.co.uk'),
	'Metro' => array('metro','metro.co.uk'),
	'The Sunday Times' => array('sundaytimes','thesundaytimes.co.uk'),
	'The Sunday Telegraph' => array('sundaytelegraph','telegraph.co.uk'),
	'The Sunday Express' => array('sundayexpress','sundayexpress.co.uk'),
	'The I' => array('thei','independent.co.uk/i/'),
	'The Independent' => array('independent','independent.co.uk'),
	'The Herald' => array('theherald','heraldscotland.com'),
	'Irish Independent' => array('irishindependent','independent.ie'),
	'Belfast Telegraph' => array('belfasttelegraph','belfasttelegraph.co.uk'),
	'The Irish Examiner' => array('irishexaminer','irishexaminer.com'),
	'Manchester Evening News' => array('manchestereveningnews','manchestereveningnews.co.uk'),
	'Yorkshire Evening Post' => array('yorkshireeveningpost', 'yorkshireeveningpost.co.uk'),
	'Financial Times' => array('financialtimes','ft.com'),
	'Times Higher Education' => array('timeshighereducation','timeshighereducation.co.uk'),
	'The Telegraph' => array('telegraph', 'www.telegraph.co.uk'),
	'ITV'=> array('itv','www.itv.com'),
	'The Times of India'=> array('timesofindia','timesofindia.indiatimes.com'),
	'Reuters'=> array('reuters','www.rt.com'),
	'Yahoo'=> array('yahoo','yahoo.com'),
	'Medical Design Technology Mag'=> array('medicalmag','www.mdtmag.com'),
	'CNN Indonesia'=> array('cnnindonesia','www.cnnindonesia.com'),
	'Japan Times'=> array('japantimes','www.japantimes.co.jp'),
	'Mongabay'=> array('mongabay','news.mongabay.com'),
	'Nature World News'=> array('natureworld','www.natureworldnews.com'),
	'Deutsche Wirtschafts Nachrichten'=> array('dwn','deutsche-wirtschafts-nachrichten.de'),
	'Mumbai Mirror'=> array('mumbaimirror','www.mumbaimirror.com'),
	'The stage'=> array('thestage','www.thestage.co.uk'),
	'Cycling News Non-stop'=> array('cyclingnews','cyclingrss.com'),
	'HNGN'=> array('hngn','www.hngn.com'),
	'Business standard'=> array('businessstandard','www.business-standard.com'),
	'Tasnim News Agency'=> array('tasnim','www.tasnimnews.com'),
	'Heart FM'=> array('heartfm','www.heart.co.uk'),
	'Medical XPress'=> array('medicalxpress','medicalxpress.com'),
	'Science news line'=> array('sciencenewsline','www.sciencenewsline.com'),
	'Time Higher Education'=> array('timeshe','www.timeshighereducation.com'),
	'International Business Times'=> array('intbustimes','www.ibtimes.co.uk'),
	'Zee news'=> array('zeenews','zeenews.india.com'),
	'Science daily'=> array('sciencedaily','www.sciencedaily.com'),
	'Disabled World'=> array('disabledworld','www.disabled-world.com'),
	'LA times'=> array('latimes','www.latimes.com'),
	'New Scientist'=> array('newscientist','www.newscientist.com'),
	'National Geographic'=> array('natgeo','news.nationalgeographic.com'),
	'Yorkshire Post'=> array('yorkshirepost','www.yorkshirepost.co.uk'),
	'Phys.org'=> array('phys','phys.org'),
	'Third Sector'=> array('thirdsector','www.thirdsector.co.uk'),
	'Wildlife Extra'=> array('wildlifeextra','www.wildlifeextra.com'),
	'Maine News'=> array('newsmaine','newsmaine.net'),
	'Kent News'=> array('kentnews','www.kentnews.co.uk'),
	'Civil Society'=> array('civilsociety','www.civilsociety.co.uk'),
	'Kent Online'=> array('kentonline','www.kentonline.co.uk'),
	'Science World Report'=> array('scienceworldreport','www.scienceworldreport.com'),
	'New York Times'=> array('nytimes','www.nytimes.com'),
	'The Sydney Morning Herald'=> array('smh','www.smh.com.au'),
	'News 24'=> array('news24','www.news24.com'),
	'Reuters UK'=> array('reutersuk','uk.reuters.com'),
	'Middle East Eye'=> array('middleeasteye','www.middleeasteye.net'),
	'The Conversation'=> array('theconversation','theconversation.com'),
	'Bangkok Post'=> array('bangkokpost','www.bangkokpost.com'),
	'Forbes'=> array('forbes','www.forbes.com'),
	'MercoPress'=> array('mercopress','mercopress.com'),
	'Gov.uk'=> array('govuk','www.gov.uk'),
	'Health Canal'=> array('healthcanal','www.healthcanal.com'),
	'The New Yorker'=> array('newyorker','www.newyorker.com'),
	'Jakarta Globe'=> array('jakartaglobe','thejakartaglobe.beritasatu.com'),
	'Net India 123'=> array('netindia123','www.netindia123.com')
);


$posts = get_posts(array(
   'post_type'=>'post',
   'post_status'=>'any',
   'posts_per_page'=> -1
));

foreach($posts as $post){

	//intro text
	update_post_meta($post->ID,'introtext',$post->post_excerpt);
	update_post_meta($post->ID,'_introtext_fields',array('introtext'));


	$fields = array();
	$related =array();

	for($i=1;$i<4;$i++) {
		$link = get_post_meta($post->ID, 'link'.$i , true);
		if(!empty($link)){
			$parts = explode(' | ',$link);
			$related[] = array('title'=>$parts[0],'url'=>$parts[1]);
		}
		delete_post_meta($post->ID, 'link'.$i);
		delete_post_meta($post->ID, '_link'.$i);
	}

	if(!empty($related)){
		update_post_meta($post->ID,'related',$related);
		$fields[] = 'related';
	}

	$cat = get_post_meta($post->ID,'primary_category',true);
	if(!empty($cat)){
		$fields[] = 'primary_category';
	}else{
		delete_post_meta($post->ID,'primary_category');
	}

	$vid = get_post_meta($post->ID,'featured_video',true);
	if(!empty($vid)){
		$fields[] = 'featured_video';
	}else{
		delete_post_meta($post->ID,'featured_video');
	}
	delete_post_meta($post->ID,'_featured_video');

	$coverage = array();

	$exisitng_coverage = get_the_terms($post->ID,'coverage');
	if(!empty($exisitng_coverage)) {
		foreach($exisitng_coverage as $term) {
			$item = [];

			$meta = get_option('taxonomy_' . $term->term_id);
			if(!empty($meta) && array_key_exists('url', $meta)) {
				$parts = explode(' | ',$term->name);
				$item['title'] = $parts[0];
				$item['url'] = $meta['url'];


				foreach($sources as $source=>$data){
					if(strpos($meta['url'], $data[1]) !== false) {
						$item['source'] = $source;
						break;
					}
				}

				if(!isset($item['source'])){
					$item['source'] = parse_url($meta['url'],PHP_URL_HOST);
				}

				$coverage[] = $item;
			}
		}
	}
	if(!empty($coverage)){
		update_post_meta($post->ID,'coverage',$coverage);
		$fields[] = 'coverage';
	}


	update_post_meta($post->ID,'_postmeta_fields',$fields);
}

echo count($posts) . " posts migrated";



foreach($sources as $source => $data){
	$t = wp_insert_term($source,'media_source',array('slug'=>$data[0]));
	add_option('taxonomy_' . $t['term_id'],array('url' => $data[1]));
}
