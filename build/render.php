<?php
$prefix = 'psbParallaxSection';
$id = wp_unique_id( "$prefix-" );

use PSB\GetCSS as GetCSS;

extract( $attributes );

// Styles 
$bgCSS = PSB\GetCSS::getBackgroundCSS($background);
$paddingCSS = PSB\GetCSS::getSpaceCSS($padding);

$mainSl = "#$id";
$styles = "
  $mainSl{
    min-height: $minHeight;
  }
  $mainSl .psbParallaxSection{
    justify-content: $verticalAlign;
    text-align: $textAlign;
    min-height: $minHeight;
    padding: $paddingCSS;
  }
  $mainSl .psbParallaxImg{
    $bgCSS
  }
";

// Style disappearing problem
global $allowedposttags;
$allowed_html = wp_parse_args( ['style' => [], 'iframe' => [
  'allowfullscreen' => true,
  'allowpaymentrequest' => true,
  'height' => true,
  'loading' => true,
  'name' => true,
  'referrerpolicy' => true,
  'sandbox' => true,
  'src' => true,
  'srcdoc' => true,
  'width' => true,
  'aria-controls' => true,
  'aria-current' => true,
  'aria-describedby' => true,
  'aria-details' => true,
  'aria-expanded' => true,
  'aria-hidden' => true,
  'aria-label' => true,
  'aria-labelledby' => true,
  'aria-live' => true,
  'class' => true,
  'data-*' => true,
  'dir' => true,
  'hidden' => true,
  'id' => true,
  'lang' => true,
  'style' => true,
  'title' => true,
  'role' => true,
  'xml:lang' => true
] ], $allowedposttags );

?>
<div
	<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() is properly escaped ?>
	<?php echo get_block_wrapper_attributes(); ?>
	id='<?php echo esc_attr( $id ); ?>'
	data-attributes='<?php echo( esc_attr( wp_json_encode( $attributes) ) ); ?>'
  data-pipecheck='<?php echo (esc_attr( wp_json_encode( psIsPremium()) ) ); ?>'
>
  <style>
    <?php echo esc_html( $styles ); ?>
  </style>

  <div class='psbParallaxImg' data-speed='<?php echo esc_attr( $speed ) ?>'></div>

  <div class='psbParallaxSection'>
    <?php echo wp_kses( $content, $allowed_html ); ?>
  </div>
</div>