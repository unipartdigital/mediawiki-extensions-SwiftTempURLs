<?php
/**
 * @file
 * @ingroup extensions
 * @author Ash Crosby <ash.crosby@unipart.io>
 * @copyright Copyright © 2022, Unipart Digital
 */

namespace MediaWiki\Extension\SwiftTempURLs;

use ThumbnailImage;

use MediaWiki\Hook\ThumbnailBeforeProduceHTMLHook;

class Hooks implements ThumbnailBeforeProduceHTMLHook {
	public function onThumbnailBeforeProduceHTML(
		$thumbnail,
		&$attribs,
		&$linkAttribs
	) {
		$file = $thumbnail->getFile();

		if ( $file ) {
			unset($attribs['srcset']);
			if ( !empty($attribs['src']) ) {
				$attribs['src'] = $file->getRepo()->getBackend()->getFileHttpUrl([
					'ttl' => 60,
					'src' => $thumbnail->getStoragePath()
				]);
			}
			if ( !empty($linkAttribs['href']) ) {
				$linkAttribs['href'] = $file->getRepo()->getBackend()->getFileHttpUrl([
					'ttl' => 60,
					'src' => $file->getPath()
				]);
			}
		}
	}
}
