<?php
namespace PSB;

class GetCSS{
	static function isValidCSS($property, $value) {
		if ( empty( $value ) && $value !== '0' && $value !== 0 ) {
			return '';
		}
		return "$property: $value;";
	}

	static function getBackgroundCSS( $bg, $isSolid = true, $isGradient = true, $isImage = true ) {
		extract( $bg );
		$type = $type ?? 'solid';
		$color = $color ?? '';
		$gradient = $gradient ?? 'linear-gradient(135deg, #0040E3, #18D4FD)';
		$image = $image ?? [];
		$position = $position ?? 'center center';
		$attachment = $attachment ?? '';
		$repeat = $repeat ?? '';
		$size = $size ?? '';
		$overlayColor = $overlayColor ?? '';

		if ( 'gradient' === $type && $isGradient ) {
			$styles = self::isValidCSS('background', $gradient);
		} elseif ( 'image' === $type && $isImage ) {
			$imgUrl = $image['url'] ?? '';
			$styles = "background: url($imgUrl);"
				. self::isValidCSS('background-color', $overlayColor)
				. self::isValidCSS('background-position', $position)
				. self::isValidCSS('background-size', $size)
				. self::isValidCSS('background-repeat', $repeat)
				. self::isValidCSS('background-attachment', $attachment)
				. self::isValidCSS('background-repeat', $repeat)
				. "background-blend-mode: overlay;";
		} else {
			$styles = $isSolid ? self::isValidCSS('background', $color) : '';
		}

		return $styles;
	}

	static function getSpaceCSS( $space ) {
		extract( $space );
		$side = $side ?? 2;
		$vertical = $vertical ?? '0px';
		$horizontal = $horizontal ?? '0px';
		$top = $top ?? '0px';
		$right = $right ?? '0px';
		$bottom = $bottom ?? '0px';
		$left = $left ?? '0px';
	
		$styles = ( 2 === $side ) ? "$vertical $horizontal" : "$top $right $bottom $left";

		return $styles;
	}
}