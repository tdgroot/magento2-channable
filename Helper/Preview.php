<?php
/**
 * Copyright © 2018 Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magmodules\Channable\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magmodules\Channable\Helper\Source as SourceHelper;
use Magento\Framework\App\Area;
use Magento\Store\Model\App\Emulation;

/**
 * Class Preview
 *
 * @package Magmodules\Channable\Helper
 */
class Preview extends AbstractHelper
{

    /**
     * @var Source
     */
    private $sourceHelper;
    /**
     * @var Emulation
     */
    private $appEmulation;

    /**
     * Preview constructor.
     *
     * @param Context   $context
     * @param Emulation $appEmulation
     * @param Source    $sourceHelper
     */
    public function __construct(
        Context $context,
        Emulation $appEmulation,
        SourceHelper $sourceHelper
    ) {
        $this->appEmulation = $appEmulation;
        $this->sourceHelper = $sourceHelper;
        parent::__construct($context);
    }

    /**
     * @param $feed
     * @param $storeId
     *
     * @return mixed|string
     */
    public function getPreviewData($feed, $storeId)
    {
        $this->appEmulation->startEnvironmentEmulation($storeId, Area::AREA_FRONTEND, true);
        $config = $this->sourceHelper->getConfig($storeId, 'preview');
        $this->appEmulation->stopEnvironmentEmulation();

        $previewTable = $this->getPreviewTable($feed, $config);
        $feedData = $this->formatFeedArrayOutput($feed);

        return $previewTable . $feedData;
    }

    /**
     * @param $feed
     * @param $config
     *
     * @return string
     */
    public function getPreviewTable($feed, $config)
    {

        $configTable = $this->getConfigTable($feed);
        $filterTable = $this->getFilterTable($config);
        $attributeTabe = $this->getAttributeTable($config);

        $hStyle = 'font-weight: bold;';
        $bStyle = 'background: #efefef;border: 1px solid #e7e7e7;';

        $html = '<h1 style="font-size: 25px;padding: 10px;border-left: 6px solid;">' . __('Config Values') . '</h1>';
        $html .= '<table width="100%" cellpadding="5" cellspacing="5">';
        $html .= '  <tr>';
        $html .= '   <td style="' . $hStyle . '">' . __('Config') . '</td>';
        $html .= '   <td style="' . $hStyle . '">' . __('Attributes') . '</td>';
        $html .= '  </tr>';
        $html .= ' <tr>';
        $html .= '  <td width="50%" valign="top" style="' . $bStyle . '">' . $configTable . $filterTable . '</td>';
        $html .= '  <td width="50%" valign="top" style="' . $bStyle . '">' . $attributeTabe . '</td>';
        $html .= ' </tr>';
        $html .= '</table>';

        return $html;
    }

    /**
     * @param $feed
     *
     * @return string
     */
    public function getConfigTable($feed)
    {
        $html = '';
        if (empty($feed['config'])) {
            return $html;
        }

        $hStyle = 'padding:2px;border-bottom: 1px solid #ffffff;font-weight: bold;';
        $bStyle = 'padding:2px;border-bottom: 1px solid #ffffff;';

        $html .= '<table width="100%" cellpadding="2" cellspacing="2">';
        $html .= ' <thead>';
        $html .= '  <tr>';
        $html .= '   <td style="' . $hStyle . '">' . __('Config') . '</td>';
        $html .= '   <td style="' . $hStyle . '">' . __('Value') . '</td>';
        $html .= '  </tr>';
        $html .= ' </thead>';
        $html .= ' <tbody>';

        foreach ($feed['config'] as $k => $v) {
            $html .= '<tr>';
            $html .= ' <td style="' . $bStyle . '" >' . $k . '</td>';
            $html .= ' <td style="' . $bStyle . '" >' . $v . '</td>';
            $html .= '</tr>';
        }

        $html .= ' </tbody>';
        $html .= '</table>';
        return $html;
    }

    /**
     * @param $config
     *
     * @return string
     */
    public function getFilterTable($config)
    {
        $html = '';
        if (empty($config['filters']['advanced'])) {
            return $html;
        }

        $fStyle = 'padding: 20px;border-bottom: 1px solid #ffffff;font-weight: bold;';
        $cStyle = 'border-bottom: 1px solid #ffffff;font-weight: bold;';

        $html .= '<table width="100%" cellpadding="2" cellspacing="2">';
        $html .= ' <tbody>';

        foreach ($config['filters']['advanced'] as $filter) {
            $attribute = $filter['attribute'];
            if ($attribute == 'quantity_and_stock_status') {
                $attribute = 'qty';
            }
            $html .= '<tr>';
            $html .= ' <td style="' . $fStyle . '" >' . __('filter') . '</td>';
            $html .= ' <td style="' . $cStyle . '" >';
            $html .= '   ' . $attribute . ' ' . $filter['condition'] . ' ' . $filter['value'] . '</td>';
            $html .= '</tr>';
        }

        $html .= ' </tbody>';
        $html .= '</table>';
        return $html;
    }

    /**
     * @param $config
     *
     * @return string
     */
    public function getAttributeTable($config)
    {
        $html = '';
        if (empty($config['attributes'])) {
            return $html;
        }

        $hStyle = 'padding:2px;border-bottom: 1px solid #ffffff;font-weight: bold;';
        $bStyle = 'padding:2px;border-bottom: 1px solid #ffffff;';

        $html .= '<table width="100%" cellpadding="2" cellspacing="2">';
        $html .= ' <thead>';
        $html .= '  <tr>';
        $html .= '   <td style="' . $hStyle . '" >' . __('Title') . '</td>';
        $html .= '   <td style="' . $hStyle . '" >' . __('Attribute') . '</td>';
        $html .= '   <td style="' . $hStyle . '" >' . __('Fallback') . '</td>';
        $html .= '  </tr>';
        $html .= ' </thead>';
        $html .= ' <tbody>';

        foreach ($config['attributes'] as $attribute) {
            if (empty($attribute['source'])) {
                continue;
            }
            $fallback = [];
            foreach ($attribute['parent'] as $k => $v) {
                if ($v == 1) {
                    $fallback[] = ucfirst($k);
                }
            }
            if (empty($fallback)) {
                $fallback[] = 'Simple';
            }
            $html .= '<tr>';
            $html .= ' <td style="' . $bStyle . '" >' . $attribute['label'] . '</td>';
            $html .= ' <td style="' . $bStyle . '" >' . $attribute['source'] . '</td>';
            $html .= ' <td style="' . $bStyle . '" >' . implode(' - ', $fallback) . '</td>';
            $html .= '</tr>';
        }

        $html .= ' </tbody>';
        $html .= '</table>';
        return $html;
    }

    /**
     * @param $feed
     *
     * @return string
     */
    public function formatFeedArrayOutput($feed)
    {
        if (empty($feed['products'])) {
            return '<h1 style="font-size: 25px;padding: 10px;border-left: 6px solid;">' . __('Feed Output') . '</h1>
            ' . __('No products found in current selection / page');
        }

        return '<h1 style="font-size: 25px;padding: 10px;border-left: 6px solid;">' . __('Feed Output') . '</h1>
            <pre>' . print_r($feed['products'], true) . '</pre>';
    }
}
