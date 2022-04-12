<?php
/**
 * @file
 * @ingroup extensions
 * @author Ash Crosby <ash.crosby@unipart.io>
 * @copyright Copyright © 2022, Unipart Digital
 */

namespace MediaWiki\Extension\SwiftTempURLs;

use MediaWiki\Hook\ThumbnailBeforeProduceHTMLHook;
use MediaWiki\Hook\LinkerMakeMediaLinkFileHook;
use Mediawiki\Extension\Diagrams\Hook\DiagramsBeforeProduceHTMLHook as DiagramsBeforeProduceHTMLHook;
use MediaWiki\MediaWikiServices;
use ThumbnailImage;
use File;

if (interface_exists(DiagramsBeforeProduceHTMLHook::class)) {
	interface DiagramsConditionalHook extends DiagramsBeforeProduceHTMLHook {}
} else {
	interface DiagramsConditionalHook {
		public function onDiagramsBeforeProduceHTML(
			File $file,
			array &$imgAttrs
		);
	}
}

class Hooks implements
	ThumbnailBeforeProduceHTMLHook,
	DiagramsConditionalHook,
	LinkerMakeMediaLinkFileHook
{
	public function onThumbnailBeforeProduceHTML(
		$thumbnail,
		&$attribs,
		&$linkAttribs
	) {
		$file = $thumbnail->getFile();

		$config = MediaWikiServices::getInstance()->getConfigFactory()->makeConfig( 'swifttempurls' );
		$ttl = $config->get( 'SwiftTempURLsTTL' );

		if ( $file ) {
			if ( !empty( $attribs['src'] ) ) {
				if ( preg_match( '/\/resources\/assets\/file-type-icons\//', $attribs['src'] ) ) {
					return;
				}
				unset($attribs['srcset']);
				$fileSrc = empty( $thumbnail->getStoragePath() )? $file->getPath(): $thumbnail->getStoragePath();
				$attribs['src'] = $file->getRepo()->getBackend()->getFileHttpUrl([
					'ttl' => $ttl,
					'src' => $fileSrc
				]);
			}
			if ( !empty( $linkAttribs['href'] ) ) {
				$linkAttribs['href'] = $file->getRepo()->getBackend()->getFileHttpUrl([
					'ttl' => $ttl,
					'src' => $file->getPath()
				]) . '&inline';
			}
		}
	}

	public function onDiagramsBeforeProduceHTML(
		File $file,
		array &$imgAttrs
	) {
		$config = MediaWikiServices::getInstance()->getConfigFactory()->makeConfig( 'swifttempurls' );
		$ttl = $config->get( 'SwiftTempURLsTTL' );

		if ( $file && !empty( $imgAttrs['src'] ) ) {
			$imgAttrs['src'] = $file->getRepo()->getBackend()->getFileHttpUrl([
				'ttl' => $ttl,
				'src' => $file->getPath()
			]) . '&inline';
		}
	}

	public function onLinkerMakeMediaLinkFile(
		$title,
		$file,
		&$html,
		&$attribs,
		&$ret
	) {
		$config = MediaWikiServices::getInstance()->getConfigFactory()->makeConfig( 'swifttempurls' );
		$ttl = $config->get( 'SwiftTempURLsTTL' );

		if ( $file && $file->exists() ) {
			$attribs['href'] = $file->getRepo()->getBackend()->getFileHttpUrl([
				'ttl' => $ttl,
				'src' => $file->getPath()
			]) . '&inline';
		}
		
		return true;
	}
}
