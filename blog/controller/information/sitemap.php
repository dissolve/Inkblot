<?php
class ControllerInformationSitemap extends Controller {
    public function index()
    {

        $this->document->setTitle(PAGE_TITLE);

        $this->data['heading_title'] = PAGE_TITLE;

        $this->data['home'] = $this->url->link('common/home');
        $this->data['team'] = $this->url->link('information/team');
        $this->data['ingredients'] = $this->url->link('information/ingredients');
        $this->data['product_list'] = $this->url->link('product/list');
        $this->data['faq'] = $this->url->link('information/faq');
        $this->data['testimonials'] = $this->url->link('information/reviews');
        $this->data['news'] = $this->url->link('information/news');
        $this->data['locator'] = $this->url->link('information/locator');
        $this->data['terms'] = $this->url->link('information/terms');
        $this->data['privacy'] = $this->url->link('information/privacy');
        $this->data['copyright'] = $this->url->link('information/copyright');

        $this->load->model('brand/product');
        $this->data['products'] = array();

        $products = $this->model_brand_product->getProducts();
        foreach ($products as $product) {
            $product_id = $product['brand_product_id'];
            $this->data['products'][] = array('name' => $product['name'],
                                              'href' => $this->url->link('products/' . $product['abbreviation']));
        }

        $this->data['brand_name'] = BRAND_NAME;
        $this->data['brand_address_1'] = BRAND_ADDRESS_1;
        $this->data['brand_address_2'] = BRAND_ADDRESS_2;
        $this->data['brand_city'] = BRAND_CITY;
        $this->data['brand_state'] = BRAND_STATE;
        $this->data['brand_zip'] = BRAND_ZIP;
        $this->data['brand_telephone'] = BRAND_PHONE;
        $this->data['brand_telephone_alt'] = ALT_BRAND_PHONE;
        $this->data['brand_support_email'] = SUPPORT_EMAIL;

        $this->data['brand_canada_address_1'] = BRAND_CANADA_ADDRESS_1;
        $this->data['brand_canada_address_2'] = BRAND_CANADA_ADDRESS_2;
        $this->data['brand_canada_city'] = BRAND_CANADA_CITY;
        $this->data['brand_canada_province'] = BRAND_CANADA_PROV;
        $this->data['brand_canada_zip'] = BRAND_CANADA_ZIP;

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/information/sitemap')) {
            $this->template = $this->config->get('config_template') . '/template/information/sitemap';
        } else {
            $this->template = 'default/template/information/sitemap';
        }

        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());
    }
}
