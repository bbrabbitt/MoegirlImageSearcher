<?php
/**
  *PictureSearcher ，萌娘百科图片源地址自动查询插件
  * @author bbrabbit
  * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License
  */
//注册插件
$wgExtensionCredits['other'][] = array(
	'path' => __FILE__,
	'name' => 'PictureSearcher',
	'author' => 'bbrabbit',
	'descriptionmsg' => '萌娘百科自动搜图插件',
	'version' => 'alpha',
);
$wgHooks['UploadComplete'][] = 'MyExtensionHooks::onUploadComplete';
class PictureSearcher{
	require_once(dirname(dirname(__FILE__).'/'.'includes'.'/'.'specials'.'SpecialUpload.php')
	public static function onUploadComplete( &$image ) { 
		global $type,$api,$minsim,$db_bitmask,$api,$url,$image_url,$image_name,$image_des,$image_desp,$log,$result,$r;
		$api="635003e481a9026e128a81d6e50ac3a913142a99";
		$type=$image->getLocalFile()->minor_mime;
		$image_des=$image->getLocalFile()->description;
		$image_desp=preg_match([\x6E90-\x5730-\x5740], $image_des)
		if($type=="jpg"|"png"|"jpeg"|"gif"&&$image_desp==0)
		{
			$url = 'http://saucenao.com/search.php?output_type=2&numres=1&minsim='.$minsim.'&dbmask='.(string)$db_bitmask+'&api_key='.$api;
			$image_url = $image->getLocalFile()->url;
			$image_name = $image->getLocalFile()->getTitle();
			$curl = curl_init($image_url);
			curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
			$imageData = curl_exec($curl);
			$tp = @fopen(dirname(__FILE__).'/'.'cache'.'/'.$image_name,'a');
			fwrite($tp, $imageData);
			fclose($tp);
			$log = fopen(dirname(__FILE__).'/'.'ImageSearcher.log','a');
			fclose($log);
			$tp = @fopen(dirname(__FILE__).'/'.'cache'.'/'.$image_name,'r');
			curl_setopt ( $get, CURLOPT_URL, $url );
			curl_setopt ( $get, CURLOPT_POST, 1 );
			curl_setopt ( $get, CURLOPT_HEADER, 0 );
			curl_setopt ( $get, CURLOPT_RETURNTRANSFER, 1 );
			curl_setopt ( $get, CURLOPT_POSTFIELDS, $tp );
			$r = curl_exec($get);
			$httpCode = curl_getinfo($get,CURLINFO_HTTP_CODE); 
			curl_close($get)
			if ($httpCode!=200)
			{
				if ($httpCode==403)
				{
					fopen(dirname(__FILE__).'/'.'ImageSearcher.log','r');
					fwrite($log,date("Y-m-d")."Incorrect or Invalid API Key! Please Edit Extension to Configure...");
					fclose($log);
				}
				elseif($httpCode==429)
				{
					fopen(dirname(__FILE__).'/'.'ImageSearcher.log','r');
					fwrite($log,date("Y-m-d")."Out of searches. Sleeping for 1 hour...");
					fclose($log);
				}
				else
				{
					$json = @fopen(dirname(__FILE__).'/'.'cache','/'.'r.txt','a');
					fwrite($json,$r);
					fclose($json);
					fopen(dirname(__FILE__).'/'.'cache','/'.'r.txt','r');
					$result = json_decode($json);
					echo "<script> var result = \"$result['source']\";</script>";
					echo "<script>result();</script>";
				}
			}
		}
	}
}
?>
<script>
function result(){
document.getElementById("wpUploadDescription").value = result;
}
</script>
