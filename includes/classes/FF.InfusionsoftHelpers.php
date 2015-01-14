<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class FF_InfusionsoftHelpers {
	/**
	 * Get all Infusionsoft Tags in a clean, well formatted, array
	 * @param  object    $app    $iSDK object from FusionForms main class (FusionForms()->app)
	 * @return array             key=>val array of all Infusionsoft Tags
	 */
	public static function tags($app, $with_categories = false) {
		if($with_categories) {
			$categories = self::tag_categories($app);
		}

		$page = 0;
		$tags = array();
		while(1) {
			$query = $app->dsQuery( 'ContactGroup', 1000, $page, array( 'Id' => '%' ), array( 'Id', 'GroupName', 'GroupCategoryId' ) );
			if(is_array($query)) {
				$tags = array_merge($tags, $query);
			}
			// error happened, break and carry on or whatever
			else {
				break;
			}

			// if results count is 1000, there might be more tags
			if(count($query) == 1000) {
				$page++;
			}
			// otherwise we're done
			else {
				break;
			}
		}

		$r = array();
		foreach($tags as $tag) {
			$r[ $tag['Id'] ] = ($with_categories) ? $categories[ $tag['GroupCategoryId'] ] . ' -> ' . $tag['GroupName'] : $tag['GroupName'];
		}

		// alpha sort results
		asort($r);

		return $r;
	}


	public static function tag_categories($app) {
		$page = 0;

		// get all the categories
		$categories = array();
		while(1) {
			$query = $app->dsQuery( 'ContactGroupCategory', 1000, $page, array( 'Id' => '%' ), array( 'Id', 'CategoryName' ) );

			if(is_array($query)) {
				$categories = array_merge($categories, $query);
			}
			// error happened, break and carry on or whatever
			else {
				break;
			}

			// if results count is 1000, there might be more groups
			if(count($query) == 1000) {
				$page++;
			}
			// otherwise we're done
			else {
				break;
			}
		}

		// move all categories into a $id=>$name pair array
		$r = array();
		foreach($categories as $cat) {
			$r[ $cat['Id'] ] = $cat['CategoryName'];
		}

		// alpha sort results
		asort($r);

		return $r;
	}
}