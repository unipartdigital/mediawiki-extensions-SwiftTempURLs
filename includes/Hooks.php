<?php
/**
 * @file
 * @ingroup extensions
 * @author Ash Crosby <ash.crosby@unipart.io>
 * @copyright Copyright © 2022, Unipart Digital
 */

namespace MediaWiki\Extension\SwiftTempURLs;

use MediaWiki\Hook\ThumbnailBeforeProduceHTMLHook;
use MediaWiki\MediaWikiServices;
use ThumbnailImage;


class Hooks implements ThumbnailBeforeProduceHTMLHook {
	public function onThumbnailBeforeProduceHTML(
		$thumbnail,
		&$attribs,
		&$linkAttribs
	) {
		$file = $thumbnail->getFile();

		$config = MediaWikiServices::getInstance()->getConfigFactory()->makeConfig( 'swifttempurls' );
		$ttl = $config->get( 'SwiftTempURLsTTL' );

		if ( $file ) {
			unset($attribs['srcset']);
			if ( !empty($attribs['src']) ) {
				$attribs['src'] = $file->getRepo()->getBackend()->getFileHttpUrl([
					'ttl' => $ttl,
					'src' => $thumbnail->getStoragePath()
				]);
			}
			if ( !empty($linkAttribs['href']) ) {
				$linkAttribs['href'] = $file->getRepo()->getBackend()->getFileHttpUrl([
					'ttl' => $ttl,
					'src' => $file->getPath()
				]) . '&inline';
			}
		}
	}
}
