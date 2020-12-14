<?php

class BrizyPro_Content_Placeholders_PostContent extends BrizyPro_Content_Placeholders_SimplePostAware
{

    /**
     * @return string|callable
     */
    protected $value;

    /**
     * BrizyPro_Content_Placeholders_PostContent constructor.
     *
     * @param $label
     * @param $placeholder
     * @param string $display
     */
    public function __construct($label, $placeholder, $display = Brizy_Content_Placeholders_Abstract::DISPLAY_INLINE)
    {
        parent::__construct($label, $placeholder, $this->getTheContentCallback(), $display);
    }

    private function getTheContentCallback()
    {
        return function ($context) {

        	$usesEditor = false;

	        try {
		        $post = Brizy_Editor_Post::get( $context->getWpPost() );

		        if ( $post->uses_editor() ) {
			        $usesEditor = true;
		        }

	        } catch ( Exception $e ) {}

            return $usesEditor ? $post->get_compiled_page()->get_body() : $context->getWpPost()->post_content;
        };
    }
}
