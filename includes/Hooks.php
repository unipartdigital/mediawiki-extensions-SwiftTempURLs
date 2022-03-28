<?php
/**
 * @file
 * @ingroup extensions
 * @author Ash Crosby <ash.crosby@unipart.io>
 * @copyright Copyright © 2022, Unipart Digital
 */

namespace MediaWiki\Extension\SwiftTempURLs;

use MediaWiki\Hook\ThumbnailBeforeProduceHTMLHook;

class Hooks implements ThumbnailBeforeProduceHTMLHook {
	public static function onThumbnailBeforeProduceHTML(
		ThumbnailImage $thumbnail,
		array &$attribs,
		&$linkAttribs
	) {
		$file = $thumbnail->getFile();

		if ( $file ) {
			$attribs['src'] = $file->getRepo()->getBackend()->getFileHttpUrl([
				'ttl' => 60,
				'src' => $file->getUrl()
			]);
		}
	}
}
