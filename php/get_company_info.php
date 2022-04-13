<?php
	if (!empty($_POST['orgnumber']) && !empty($_POST['info'])) {
		$company = getCompanyInfo($_POST['orgnumber'], $_POST['info']);
		if ($company === false) {
			echo 'null';
		} else {
			echo $company;
		}
	}
	function getCompanyInfo($orgNumber, $info) {
		if (!empty($orgNumber)) {
			$orgNumber = str_replace('-', '', $orgNumber);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_URL, "https://www.allabolag.se/". $orgNumber ."/");
			$data = curl_exec($ch);
			curl_close($ch);
			
			if ($data === false) {
				return false;
			}			
			$name = '';
			$number = '';
			$adress = '';
			$postalcode = '';
			$city = '';
			
			switch ($info) {				
				case "name":
					if (preg_match('#<h1 class="p-name">(.*)</h1>#', $data, $match)) {
						$name = $match[1];
						return $name;
					} else {
						return false;
					}
					
				case "number":
					if (preg_match('#<a class="p-tel" href="tel:(.*)">#', $data, $match)) {
						$number = $match[1];
						return $number;
					} else {
						return false;
					}
					
				case "adress":
					if (preg_match('#<a class="desktop-only(.*)<span class="icon--right-s icon icon--place">#', $data, $match)) {
						$adress = $match[1];
						return $adress;
					} else {
						return false;
					}
					
				case "postalcode":
					if (preg_match('#<span class="p-postal-code">(.*)</span>#', $data, $match)) {
						$postalcode = $match[1];
						return $postalcode;
					} else {
						return false;
					}
					
				case "city":
					if (preg_match('#<span class="p-locality"> (.*)</span>#', $data, $match)) {
						$city = $match[1];
						return $city;
					} else {
						return false;
					}
					
				default:
					if (preg_match('#<h1 class="p-name">(.*)</h1>#', $data, $match)) {
						$name = $match[1];
					} else {
						return false;
					}

					if (preg_match('#<a class="p-tel" href="tel:(.*)">#', $data, $match)) {
						$number = $match[1];
					} else {
						return false;
					}

					if (preg_match('#<h1 class="p-name">(.*)</h1>#', $data, $match)) {
						$adress = $match[1];
					} else {
						return false;
					}

					if (preg_match('#<span class="p-postal-code">(.*)</span>#', $data, $match)) {
						$postalcode = $match[1];
					} else {
						return false;
					}

					if (preg_match('#<span class="p-locality"> (.*)</span>#', $data, $match)) {
						$city = $match[1];
					} else {
						return false;
					}
					return $arr = array($name, $number, $adress, $postalcode, $city);
			}			
		}
		return false;
	}
?>