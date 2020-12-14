<?php

class BrizyPro_Content_Placeholders_PostLoop extends Brizy_Content_Placeholders_Abstract
{

    /**
     * @var
     */
    private $twig;

    /**
     * BrizyPro_Content_Placeholders_PostLoop constructor.
	 *
	 * @param string $label
	 * @param string $placeholder
	 *
     * @throws Exception
     */
	public function __construct($label, $placeholder) {
		$this->setLabel( $label );
		$this->setPlaceholder( $placeholder );
        $this->setDisplay(self::DISPLAY_BLOCK);
        $this->twig = Brizy_TwigEngine::instance(BRIZY_PRO_PLUGIN_PATH."/content/views/");
    }

    /**
     * @param Brizy_Content_ContentPlaceholder $contentPlaceholder
     * @param Brizy_Content_Context $context
     *
     * @return false|mixed|string
     */
    public function getValue(Brizy_Content_Context $context, Brizy_Content_ContentPlaceholder $contentPlaceholder)
    {

        $attributes    = $contentPlaceholder->getAttributes();
        $posts         = $this->getPosts($attributes);
        $globalProduct = isset($GLOBALS['product']) ? $GLOBALS['product'] : null;
        $content       = '';

        foreach ((array)$posts as $post) {
            if ('product' === $post->post_type) {
                $GLOBALS['product'] = wc_get_product($post);
            }

            $content .= $this->getItemContent($post, $context, $contentPlaceholder);
        }

        if (isset($GLOBALS['product'])) {

            unset($GLOBALS['product']);

            if ($globalProduct) {
                $GLOBALS['product'] = $globalProduct;
            }
        }

        return $content;
    }

    /**
     * @param Wp_Post $post
     * @param Brizy_Content_Context $context
     * @param Brizy_Content_ContentPlaceholder $contentPlaceholder
     *
     * @return mixed|string
     */
    protected function getItemContent($post, $context, $contentPlaceholder)
    {
        try {
            $newContext = Brizy_Content_ContextFactory::createContext($context->getProject(), null, $post, null, true);
            Brizy_Content_ContextFactory::makeContextGlobal($newContext);

            $placeholderProvider = new Brizy_Content_PlaceholderProvider($newContext);
            $extractor           = new Brizy_Content_PlaceholderExtractor($placeholderProvider);

            list($placeholders, $acontent) = $extractor->extract($contentPlaceholder->getContent());

            $replacer = new Brizy_Content_PlaceholderReplacer($newContext, $placeholderProvider, $extractor);

            $content = do_shortcode($replacer->getContent($placeholders, $acontent));

            Brizy_Content_ContextFactory::clearGlobalContext();

            return $content;
        } catch (Exception $e) {
            return '';
        }
    }

    /**
     * @return mixed|string
     */
    protected function getOptionValue()
    {
        return $this->getReplacePlaceholder();
    }

    /**
     * @param $attributes
     *
     * @return array
     */
	protected function getPosts( $attributes ) {
        $paged = $this->getPageVar();
        $query = null;
        if (isset($attributes['query']) && ! empty($attributes['query'])) {
            $params = array_merge(
                array(
                    'posts_per_page' => isset($attributes['count']) ? $attributes['count'] : 3,
                    'orderby'        => isset($attributes['orderby']) ? $attributes['orderby'] : 'none',
                    'order'          => isset($attributes['order']) ? $attributes['order'] : 'ASC',
				'post_type'      => isset( $attributes['post_type'] ) ? $attributes['post_type'] : array_keys( get_post_types( [ 'public' => true ] ) ),
                    'paged'          => $paged,
                ),
                wp_parse_args($attributes['query'])
            );

            $query  = new WP_Query($params);

        } else {
            global $wp_query;
            $queryVars                   = $wp_query->query_vars;
            $queryVars['orderby']        = isset($attributes['orderby']) ? $attributes['orderby'] : (isset($queryVars['orderby']) ? $queryVars['orderby'] : null);
            $queryVars['order']          = isset($attributes['order']) ? $attributes['order'] : (isset($queryVars['order']) ? $queryVars['order'] : null);
            $queryVars['posts_per_page'] = isset($attributes['count']) ? (int)$attributes['count'] : (isset($queryVars['posts_per_page']) ? $queryVars['posts_per_page'] : null);
            $queryVars['post_type']      = isset($attributes['post_type']) ? $attributes['post_type'] : (isset($queryVars['post_type']) ? $queryVars['post_type'] : null);
            $queryVars['paged']          = (int)$paged;

            $query                       = new WP_Query($queryVars);
        }

        $posts = $query->posts;

        wp_reset_postdata();

        return $posts;
    }

    /**
     * @return int|mixed
     */
    private function getPageVar()
    {
        if ($paged = get_query_var(self::getPaginationKey())) {
            return (int)$paged;
        }

        return 1;
    }


    /**
     * Return the pagination key. bpage is the default value.
     *
     * @return mixed|void
     */
    public static function getPaginationKey()
    {
        return apply_filters('brizy_postloop_pagination_key', 'bpage');
    }
}

