<?php

include('SimpleHtmlDom.php');

class Scrape
{
	protected $http_code = 200;
	
	/**
	 *  POST Method - Initialize Ajax Request
	 *  
	 *  @return json
	 */
	public function ajaxSubmit()
	{
		if( isset($_POST['url']) ) {
			
			$url 	= trim($_POST['url']);
			$fields = trim($_POST['fields']);
			
			if( !empty($url) ) {
				
				$get_url = parse_url($url);				
				$get_url = isset($get_url['host']) ? $get_url['host'] : $get_url['path'];					
				$get_url = explode('.', $get_url);
				
				if( $get_url[1] == 'linkedin' ) {
					
					$response = trim($this->_scrape($url));	
					
					if( $this->http_code == '404' ) {
						
						header( 'HTTP/1.1 404 LinkedIn Profile Not Found' );
						exit();				
					}		
					
					preg_match( '/<main id="layout-main" role="main">(.*?)<\/main>/s', 
								$response, 
								$html
							);			
					$response_code = '<small><strong>HTTP RESPONSE CODE: </strong>' . $this->http_code . '</small><br><br>';
					$response = $html[0];					
					
					if( !empty($fields) )
						return $response_code . $this->_parse($response, $fields);

					return $response_code . $response;
				}
				
				header( 'HTTP/1.1 400 Invalid LinkedIn URL' );
				exit();	
			}			
		}
	}
	
	/**
	 *  Curl function to get html content
	 *  
	 *  @param string $url
	 *  
	 *  @return string
	 */
	private function _scrape( $url )
	{
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		
		$content = curl_exec($ch);
		$this->http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		
		curl_close($ch);
		
		return $content;
	}
	
	/**
	 *  Parse Fields from Response
	 *  
	 *  @param string $response
	 *  @param string $fields
	 *  
	 *  @return string
	 */
	private function _parse( $response, $fields )
	{
		$SimpleHtmlDom = new SimpleHtmlDom;						
		$SimpleHtmlDom->load($response);
		
		$name = $SimpleHtmlDom->find("#name", 0)->plaintext;		
		$name = explode(' ', $name, 2);		
		
		$available_fields = [		
						'firstname' => $name[0], 
						'lastname'  => $name[1], 
						'headline'  => $SimpleHtmlDom->find("p[class=headline title]", 0)->plaintext, 
						'locality'  => $SimpleHtmlDom->find(".locality", 0)->plaintext
						
						// etc..
					];
		
		$result = '<dl class="dl-horizontal">';			
		
		foreach( explode(',', $fields) as $field ) {
			
			$field = trim(strtolower($field));
			$result .= "<dt>".ucfirst($field)."</dt><dd>".(isset($available_fields[$field])?$available_fields[$field]:'')."</dd>";	
		}
		
		$result .= '</dl>';
		
		return $result;
	}
}
print (new Scrape)->ajaxSubmit();