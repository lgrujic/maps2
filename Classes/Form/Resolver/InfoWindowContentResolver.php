<?php
namespace JWeiland\Maps2\Form\Resolver;

/*
 * This file is part of the maps2 project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use JWeiland\Maps2\Form\Element\InfoWindowCkEditorElement;
use JWeiland\Maps2\Form\Element\InfoWindowCkEditorElement87;
use TYPO3\CMS\Backend\Form\Element\TextElement;
use TYPO3\CMS\Backend\Form\NodeFactory;
use TYPO3\CMS\Backend\Form\NodeResolverInterface;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;

/**
 * This resolver will return the RichTextElement render class of ext:rtehtmlarea or EXT:ckeditor if RTE is enabled
 * for this field.
 */
class InfoWindowContentResolver implements NodeResolverInterface
{
    /**
     * Global options from NodeFactory
     *
     * @var array
     */
    protected $data = [];

    /**
     * Default constructor receives full data array
     *
     * @param NodeFactory $nodeFactory
     * @param array $data
     */
    public function __construct(NodeFactory $nodeFactory, array $data)
    {
        $this->data = $data;
    }

    /**
     * Returns RichTextElement as class name if RTE widget should be rendered.
     *
     * @return string|null New class name or void if this resolver does not change current class name.
     */
    public function resolve()
    {
        $parameterArray = $this->data['parameterArray'];
        $backendUser = $this->getBackendUserAuthentication();
        if (// This field is not read only
            !$parameterArray['fieldConf']['config']['readOnly']
            // If RTE is generally enabled by user settings and RTE object registry can return something valid
            && $backendUser->isRTE()
            // If RTE is enabled for field
            && isset($parameterArray['fieldConf']['config']['enableRichtext'])
            && (bool)$parameterArray['fieldConf']['config']['enableRichtext'] === true
            // If RTE config is found (prepared by TcaText data provider)
            && isset($parameterArray['fieldConf']['config']['richtextConfiguration'])
            && is_array($parameterArray['fieldConf']['config']['richtextConfiguration'])
            // If RTE is not disabled on configuration level
            && !$parameterArray['fieldConf']['config']['richtextConfiguration']['disabled']
        ) {
            if (version_compare(TYPO3_branch, '8.7') <= 0) {
                return InfoWindowCkEditorElement87::class;
            } else {
                return InfoWindowCkEditorElement::class;
            }
        }

        return TextElement::class;
    }

    /**
     * @return BackendUserAuthentication
     */
    protected function getBackendUserAuthentication()
    {
        return $GLOBALS['BE_USER'];
    }
}
