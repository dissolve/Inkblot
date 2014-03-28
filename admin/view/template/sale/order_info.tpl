<?php echo $header; ?><?php echo $menu; ?>
<div id="content">
  <ul class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
    <?php } ?>
  </ul>
  <div class="panel panel-default">
    <div class="panel-heading">
      <div class="pull-right">
        <button type="button" id="button-restock" data-toggle="tooltip" data-placement="left" title="<?php echo $button_restock; ?>" class="btn"><i class="fa fa-undo"></i></button>
        <a href="<?php echo $invoice; ?>" target="_blank" data-toggle="tooltip" title="<?php echo $button_invoice; ?>" class="btn"><i class="fa fa-print"></i></a> <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn"><i class="fa fa-reply"></i></a></div>
      <h1 class="panel-title"><i class="fa fa-info-circle fa-lg"></i> <?php echo $heading_title; ?></h1>
    </div>
    <div class="panel-body">
      <ul class="nav nav-tabs">
        <li class="active"><a href="#tab-order" data-toggle="tab"><?php echo $tab_order; ?></a></li>
        <li><a href="#tab-payment" data-toggle="tab"><?php echo $tab_payment; ?></a></li>
        <?php if ($shipping_method) { ?>
        <li><a href="#tab-shipping" data-toggle="tab"><?php echo $tab_shipping; ?></a></li>
        <?php } ?>
        <li><a href="#tab-product" data-toggle="tab"><?php echo $tab_product; ?></a></li>
        <li><a href="#tab-history" data-toggle="tab"><?php echo $tab_history; ?></a></li>
        <?php if ($maxmind_id) { ?>
        <li><a href="#tab-fraud" data-toggle="tab"><?php echo $tab_fraud; ?></a></li>
        <?php } ?>
      </ul>
      <div class="tab-content">
        <div class="tab-pane active" id="tab-order">
          <table class="table table-bordered">
            <tr>
              <td><?php echo $text_order_id; ?></td>
              <td>#<?php echo $order_id; ?></td>
            </tr>
            <tr>
              <td><?php echo $text_invoice_no; ?></td>
              <td><?php if ($invoice_no) { ?>
                <?php echo $invoice_no; ?>
                <?php } else { ?>
                <button id="button-invoice" class="btn btn-success btn-xs"><i class="fa fa-cog"></i> <?php echo $button_generate; ?></button>
                <?php } ?></td>
            </tr>
            <tr>
              <td><?php echo $text_store_name; ?></td>
              <td><?php echo $store_name; ?></td>
            </tr>
            <tr>
              <td><?php echo $text_store_url; ?></td>
              <td><a href="<?php echo $store_url; ?>" target="_blank"><?php echo $store_url; ?></a></td>
            </tr>
            <?php if ($customer) { ?>
            <tr>
              <td><?php echo $text_customer; ?></td>
              <td><a href="<?php echo $customer; ?>" target="_blank"><?php echo $firstname; ?> <?php echo $lastname; ?></a></td>
            </tr>
            <?php } else { ?>
            <tr>
              <td><?php echo $text_customer; ?></td>
              <td><?php echo $firstname; ?> <?php echo $lastname; ?></td>
            </tr>
            <?php } ?>
            <?php if ($customer_group) { ?>
            <tr>
              <td><?php echo $text_customer_group; ?></td>
              <td><?php echo $customer_group; ?></td>
            </tr>
            <?php } ?>
            <tr>
              <td><?php echo $text_email; ?></td>
              <td><a href="mailto:<?php echo $email; ?>"><?php echo $email; ?></a></td>
            </tr>
            <tr>
              <td><?php echo $text_telephone; ?></td>
              <td><?php echo $telephone; ?></td>
            </tr>
            <?php if ($fax) { ?>
            <tr>
              <td><?php echo $text_fax; ?></td>
              <td><?php echo $fax; ?></td>
            </tr>
            <?php } ?>
            <tr>
              <td><?php echo $text_total; ?></td>
              <td><?php echo $total; ?></td>
            </tr>
            <?php if ($customer && $reward) { ?>
            <tr>
              <td><?php echo $text_reward; ?></td>
              <td><?php echo $reward; ?>
                <?php if (!$reward_total) { ?>
                <button id="button-reward-add" class="btn btn-success btn-xs"><i class="fa fa-plus-circle"></i> <?php echo $button_reward_add; ?></button>
                <?php } else { ?>
                <button id="button-reward-remove" class="btn btn-danger btn-xs"><i class="fa fa-minus-circle"></i> <?php echo $button_reward_remove; ?></button>
                <?php } ?></td>
            </tr>
            <?php } ?>
            <?php if ($order_status) { ?>
            <tr>
              <td><?php echo $text_order_status; ?></td>
              <td id="order-status"><?php echo $order_status; ?></td>
            </tr>
            <?php } ?>
            <?php if ($comment) { ?>
            <tr>
              <td><?php echo $text_comment; ?></td>
              <td><?php echo $comment; ?></td>
            </tr>
            <?php } ?>
            <?php if ($affiliate) { ?>
            <tr>
              <td><?php echo $text_affiliate; ?></td>
              <td><a href="<?php echo $affiliate; ?>"><?php echo $affiliate_firstname; ?> <?php echo $affiliate_lastname; ?></a></td>
            </tr>
            <tr>
              <td><?php echo $text_commission; ?></td>
              <td><?php echo $commission; ?>
                <?php if (!$commission_total) { ?>
                <button id="button-commission-add" class="btn btn-success btn-xs"><i class="fa fa-plus-circle"></i> <?php echo $button_commission_add; ?></button>
                <?php } else { ?>
                <button id="button-commission-remove" class="btn btn-danger btn-xs"><i class="fa fa-minus-circle"></i> <?php echo $button_commission_remove; ?></button>
                <?php } ?></td>
            </tr>
            <?php } ?>
            <?php if ($ip) { ?>
            <tr>
              <td><?php echo $text_ip; ?></td>
              <td><?php echo $ip; ?></td>
            </tr>
            <?php } ?>
            <?php if ($forwarded_ip) { ?>
            <tr>
              <td><?php echo $text_forwarded_ip; ?></td>
              <td><?php echo $forwarded_ip; ?></td>
            </tr>
            <?php } ?>
            <?php if ($user_agent) { ?>
            <tr>
              <td><?php echo $text_user_agent; ?></td>
              <td><?php echo $user_agent; ?></td>
            </tr>
            <?php } ?>
            <?php if ($accept_language) { ?>
            <tr>
              <td><?php echo $text_accept_language; ?></td>
              <td><?php echo $accept_language; ?></td>
            </tr>
            <?php } ?>
            <tr>
              <td><?php echo $text_date_added; ?></td>
              <td><?php echo $date_added; ?></td>
            </tr>
            <tr>
              <td><?php echo $text_date_modified; ?></td>
              <td><?php echo $date_modified; ?></td>
            </tr>
          </table>
        </div>
        <div class="tab-pane" id="tab-payment">
          <table class="table table-bordered">
            <tr>
              <td><?php echo $text_firstname; ?></td>
              <td><?php echo $payment_firstname; ?></td>
            </tr>
            <tr>
              <td><?php echo $text_lastname; ?></td>
              <td><?php echo $payment_lastname; ?></td>
            </tr>
            <?php if ($payment_company) { ?>
            <tr>
              <td><?php echo $text_company; ?></td>
              <td><?php echo $payment_company; ?></td>
            </tr>
            <?php } ?>
            <tr>
              <td><?php echo $text_address_1; ?></td>
              <td><?php echo $payment_address_1; ?></td>
            </tr>
            <?php if ($payment_address_2) { ?>
            <tr>
              <td><?php echo $text_address_2; ?></td>
              <td><?php echo $payment_address_2; ?></td>
            </tr>
            <?php } ?>
            <tr>
              <td><?php echo $text_city; ?></td>
              <td><?php echo $payment_city; ?></td>
            </tr>
            <?php if ($payment_postcode) { ?>
            <tr>
              <td><?php echo $text_postcode; ?></td>
              <td><?php echo $payment_postcode; ?></td>
            </tr>
            <?php } ?>
            <tr>
              <td><?php echo $text_zone; ?></td>
              <td><?php echo $payment_zone; ?></td>
            </tr>
            <?php if ($payment_zone_code) { ?>
            <tr>
              <td><?php echo $text_zone_code; ?></td>
              <td><?php echo $payment_zone_code; ?></td>
            </tr>
            <?php } ?>
            <tr>
              <td><?php echo $text_country; ?></td>
              <td><?php echo $payment_country; ?></td>
            </tr>
            <tr>
              <td><?php echo $text_payment_method; ?></td>
              <td><?php echo $payment_method; ?></td>
            </tr>
          </table>
          <?php echo $payment_action; ?></div>
        <?php if ($shipping_method) { ?>
        <div class="tab-pane" id="tab-shipping">
          <table class="table table-bordered">
            <tr>
              <td><?php echo $text_firstname; ?></td>
              <td><?php echo $shipping_firstname; ?></td>
            </tr>
            <tr>
              <td><?php echo $text_lastname; ?></td>
              <td><?php echo $shipping_lastname; ?></td>
            </tr>
            <?php if ($shipping_company) { ?>
            <tr>
              <td><?php echo $text_company; ?></td>
              <td><?php echo $shipping_company; ?></td>
            </tr>
            <?php } ?>
            <tr>
              <td><?php echo $text_address_1; ?></td>
              <td><?php echo $shipping_address_1; ?></td>
            </tr>
            <?php if ($shipping_address_2) { ?>
            <tr>
              <td><?php echo $text_address_2; ?></td>
              <td><?php echo $shipping_address_2; ?></td>
            </tr>
            <?php } ?>
            <tr>
              <td><?php echo $text_city; ?></td>
              <td><?php echo $shipping_city; ?></td>
            </tr>
            <?php if ($shipping_postcode) { ?>
            <tr>
              <td><?php echo $text_postcode; ?></td>
              <td><?php echo $shipping_postcode; ?></td>
            </tr>
            <?php } ?>
            <tr>
              <td><?php echo $text_zone; ?></td>
              <td><?php echo $shipping_zone; ?></td>
            </tr>
            <?php if ($shipping_zone_code) { ?>
            <tr>
              <td><?php echo $text_zone_code; ?></td>
              <td><?php echo $shipping_zone_code; ?></td>
            </tr>
            <?php } ?>
            <tr>
              <td><?php echo $text_country; ?></td>
              <td><?php echo $shipping_country; ?></td>
            </tr>
            <?php if ($shipping_method) { ?>
            <tr>
              <td><?php echo $text_shipping_method; ?></td>
              <td><?php echo $shipping_method; ?></td>
            </tr>
            <?php } ?>
          </table>
        </div>
        <?php } ?>
        <div class="tab-pane" id="tab-product">
          <table class="table table-bordered">
            <thead>
              <tr>
                <td class="text-left"><?php echo $column_product; ?></td>
                <td class="text-left"><?php echo $column_model; ?></td>
                <td class="text-right"><?php echo $column_quantity; ?></td>
                <td class="text-right"><?php echo $column_price; ?></td>
                <td class="text-right"><?php echo $column_total; ?></td>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($products as $product) { ?>
              <tr>
                <td class="text-left"><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a>
                  <?php foreach ($product['option'] as $option) { ?>
                  <br />
                  <?php if ($option['type'] != 'file') { ?>
                  &nbsp;<small> - <?php echo $option['name']; ?>: <?php echo $option['value']; ?></small>
                  <?php } else { ?>
                  &nbsp;<small> - <?php echo $option['name']; ?>: <a href="<?php echo $option['href']; ?>"><?php echo $option['value']; ?></a></small>
                  <?php } ?>
                  <?php } ?></td>
                <td class="text-left"><?php echo $product['model']; ?></td>
                <td class="text-right"><?php echo $product['quantity']; ?></td>
                <td class="text-right"><?php echo $product['price']; ?></td>
                <td class="text-right"><?php echo $product['total']; ?></td>
              </tr>
              <?php } ?>
              <?php foreach ($vouchers as $voucher) { ?>
              <tr>
                <td class="text-left"><a href="<?php echo $voucher['href']; ?>"><?php echo $voucher['description']; ?></a></td>
                <td class="text-left"></td>
                <td class="text-right">1</td>
                <td class="text-right"><?php echo $voucher['amount']; ?></td>
                <td class="text-right"><?php echo $voucher['amount']; ?></td>
              </tr>
              <?php } ?>
              <?php foreach ($totals as $totals) { ?>
              <tr>
                <td colspan="4" class="text-right"><?php echo $totals['title']; ?>:</td>
                <td class="text-right"><?php echo $totals['text']; ?></td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
        <div class="tab-pane " id="tab-history">
          <div id="history"></div>
          <br />
          <form class="form-horizontal">
            <div class="form-group">
              <label class="col-sm-2 control-label" for="input-order-status"><?php echo $entry_order_status; ?></label>
              <div class="col-sm-10">
                <select name="order_status_id" id="input-order-status" class="form-control">
                  <?php foreach ($order_statuses as $order_statuses) { ?>
                  <?php if ($order_statuses['order_status_id'] == $order_status_id) { ?>
                  <option value="<?php echo $order_statuses['order_status_id']; ?>" selected="selected"><?php echo $order_statuses['name']; ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $order_statuses['order_status_id']; ?>"><?php echo $order_statuses['name']; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label" for="input-notify"><?php echo $entry_notify; ?></label>
              <div class="col-sm-10">
                <input type="checkbox" name="notify" value="1" id="input-notify" />
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label" for="input-comment"><?php echo $entry_comment; ?></label>
              <div class="col-sm-10">
                <textarea name="comment" rows="8" id="input-comment" class="form-control"></textarea>
              </div>
            </div>
          </form>
          <div class="text-right">
            <button id="button-history" class="btn btn-primary"><i class="fa fa-plus-circle"></i> <?php echo $button_history_add; ?></button>
          </div>
        </div>
        <?php if ($maxmind_id) { ?>
        <div class="tab-pane" id="tab-fraud">
          <table class="table table-bordered">
            <?php if ($country_match) { ?>
            <tr>
              <td><?php echo $text_country_match; ?></td>
              <td><?php echo $country_match; ?></td>
            </tr>
            <?php } ?>
            <?php if ($country_code) { ?>
            <tr>
              <td><?php echo $text_country_code; ?></td>
              <td><?php echo $country_code; ?></td>
            </tr>
            <?php } ?>
            <?php if ($high_risk_country) { ?>
            <tr>
              <td><?php echo $text_high_risk_country; ?></td>
              <td><?php echo $high_risk_country; ?></td>
            </tr>
            <?php } ?>
            <?php if ($distance) { ?>
            <tr>
              <td><?php echo $text_distance; ?></td>
              <td><?php echo $distance; ?></td>
            </tr>
            <?php } ?>
            <?php if ($ip_region) { ?>
            <tr>
              <td><?php echo $text_ip_region; ?></td>
              <td><?php echo $ip_region; ?></td>
            </tr>
            <?php } ?>
            <?php if ($ip_city) { ?>
            <tr>
              <td><?php echo $text_ip_city; ?></td>
              <td><?php echo $ip_city; ?></td>
            </tr>
            <?php } ?>
            <?php if ($ip_latitude) { ?>
            <tr>
              <td><?php echo $text_ip_latitude; ?></td>
              <td><?php echo $ip_latitude; ?></td>
            </tr>
            <?php } ?>
            <?php if ($ip_longitude) { ?>
            <tr>
              <td><?php echo $text_ip_longitude; ?></td>
              <td><?php echo $ip_longitude; ?></td>
            </tr>
            <?php } ?>
            <?php if ($ip_isp) { ?>
            <tr>
              <td><?php echo $text_ip_isp; ?></td>
              <td><?php echo $ip_isp; ?></td>
            </tr>
            <?php } ?>
            <?php if ($ip_org) { ?>
            <tr>
              <td><?php echo $text_ip_org; ?></td>
              <td><?php echo $ip_org; ?></td>
            </tr>
            <?php } ?>
            <?php if ($ip_asnum) { ?>
            <tr>
              <td><?php echo $text_ip_asnum; ?></td>
              <td><?php echo $ip_asnum; ?></td>
            </tr>
            <?php } ?>
            <?php if ($ip_user_type) { ?>
            <tr>
              <td><?php echo $text_ip_user_type; ?></td>
              <td><?php echo $ip_user_type; ?></td>
            </tr>
            <?php } ?>
            <?php if ($ip_country_confidence) { ?>
            <tr>
              <td><?php echo $text_ip_country_confidence; ?></td>
              <td><?php echo $ip_country_confidence; ?></td>
            </tr>
            <?php } ?>
            <?php if ($ip_region_confidence) { ?>
            <tr>
              <td><?php echo $text_ip_region_confidence; ?></td>
              <td><?php echo $ip_region_confidence; ?></td>
            </tr>
            <?php } ?>
            <?php if ($ip_city_confidence) { ?>
            <tr>
              <td><?php echo $text_ip_city_confidence; ?></td>
              <td><?php echo $ip_city_confidence; ?></td>
            </tr>
            <?php } ?>
            <?php if ($ip_postal_confidence) { ?>
            <tr>
              <td><?php echo $text_ip_postal_confidence; ?></td>
              <td><?php echo $ip_postal_confidence; ?></td>
            </tr>
            <?php } ?>
            <?php if ($ip_postal_code) { ?>
            <tr>
              <td><?php echo $text_ip_postal_code; ?></td>
              <td><?php echo $ip_postal_code; ?></td>
            </tr>
            <?php } ?>
            <?php if ($ip_accuracy_radius) { ?>
            <tr>
              <td><?php echo $text_ip_accuracy_radius; ?></td>
              <td><?php echo $ip_accuracy_radius; ?></td>
            </tr>
            <?php } ?>
            <?php if ($ip_net_speed_cell) { ?>
            <tr>
              <td><?php echo $text_ip_net_speed_cell; ?></td>
              <td><?php echo $ip_net_speed_cell; ?></td>
            </tr>
            <?php } ?>
            <?php if ($ip_metro_code) { ?>
            <tr>
              <td><?php echo $text_ip_metro_code; ?></td>
              <td><?php echo $ip_metro_code; ?></td>
            </tr>
            <?php } ?>
            <?php if ($ip_area_code) { ?>
            <tr>
              <td><?php echo $text_ip_area_code; ?></td>
              <td><?php echo $ip_area_code; ?></td>
            </tr>
            <?php } ?>
            <?php if ($ip_time_zone) { ?>
            <tr>
              <td><?php echo $text_ip_time_zone; ?></td>
              <td><?php echo $ip_time_zone; ?></td>
            </tr>
            <?php } ?>
            <?php if ($ip_region_name) { ?>
            <tr>
              <td><?php echo $text_ip_region_name; ?></td>
              <td><?php echo $ip_region_name; ?></td>
            </tr>
            <?php } ?>
            <?php if ($ip_domain) { ?>
            <tr>
              <td><?php echo $text_ip_domain; ?></td>
              <td><?php echo $ip_domain; ?></td>
            </tr>
            <?php } ?>
            <?php if ($ip_country_name) { ?>
            <tr>
              <td><?php echo $text_ip_country_name; ?></td>
              <td><?php echo $ip_country_name; ?></td>
            </tr>
            <?php } ?>
            <?php if ($ip_continent_code) { ?>
            <tr>
              <td><?php echo $text_ip_continent_code; ?></td>
              <td><?php echo $ip_continent_code; ?></td>
            </tr>
            <?php } ?>
            <?php if ($ip_corporate_proxy) { ?>
            <tr>
              <td><?php echo $text_ip_corporate_proxy; ?></td>
              <td><?php echo $ip_corporate_proxy; ?></td>
            </tr>
            <?php } ?>
            <?php if ($anonymous_proxy) { ?>
            <tr>
              <td><?php echo $text_anonymous_proxy; ?></td>
              <td><?php echo $anonymous_proxy; ?></td>
            </tr>
            <?php } ?>
            <?php if ($proxy_score) { ?>
            <tr>
              <td><?php echo $text_proxy_score; ?></td>
              <td><?php echo $proxy_score; ?></td>
            </tr>
            <?php } ?>
            <?php if ($is_trans_proxy) { ?>
            <tr>
              <td><?php echo $text_is_trans_proxy; ?></td>
              <td><?php echo $is_trans_proxy; ?></td>
            </tr>
            <?php } ?>
            <?php if ($free_mail) { ?>
            <tr>
              <td><?php echo $text_free_mail; ?></td>
              <td><?php echo $free_mail; ?></td>
            </tr>
            <?php } ?>
            <?php if ($carder_email) { ?>
            <tr>
              <td><?php echo $text_carder_email; ?></td>
              <td><?php echo $carder_email; ?></td>
            </tr>
            <?php } ?>
            <?php if ($high_risk_username) { ?>
            <tr>
              <td><?php echo $text_high_risk_username; ?></td>
              <td><?php echo $high_risk_username; ?></td>
            </tr>
            <?php } ?>
            <?php if ($high_risk_password) { ?>
            <tr>
              <td><?php echo $text_high_risk_password; ?></td>
              <td><?php echo $high_risk_password; ?></td>
            </tr>
            <?php } ?>
            <?php if ($bin_match) { ?>
            <tr>
              <td><?php echo $text_bin_match; ?></td>
              <td><?php echo $bin_match; ?></td>
            </tr>
            <?php } ?>
            <?php if ($bin_country) { ?>
            <tr>
              <td><?php echo $text_bin_country; ?></td>
              <td><?php echo $bin_country; ?></td>
            </tr>
            <?php } ?>
            <?php if ($bin_name_match) { ?>
            <tr>
              <td><?php echo $text_bin_name_match; ?></td>
              <td><?php echo $bin_name_match; ?></td>
            </tr>
            <?php } ?>
            <?php if ($bin_name) { ?>
            <tr>
              <td><?php echo $text_bin_name; ?></td>
              <td><?php echo $bin_name; ?></td>
            </tr>
            <?php } ?>
            <?php if ($bin_phone_match) { ?>
            <tr>
              <td><?php echo $text_bin_phone_match; ?></td>
              <td><?php echo $bin_phone_match; ?></td>
            </tr>
            <?php } ?>
            <?php if ($bin_phone) { ?>
            <tr>
              <td><?php echo $text_bin_phone; ?></td>
              <td><?php echo $bin_phone; ?></td>
            </tr>
            <?php } ?>
            <?php if ($customer_phone_in_billing_location) { ?>
            <tr>
              <td><?php echo $text_customer_phone_in_billing_location; ?></td>
              <td><?php echo $customer_phone_in_billing_location; ?></td>
            </tr>
            <?php } ?>
            <?php if ($ship_forward) { ?>
            <tr>
              <td><?php echo $text_ship_forward; ?></td>
              <td><?php echo $ship_forward; ?></td>
            </tr>
            <?php } ?>
            <?php if ($city_postal_match) { ?>
            <tr>
              <td><?php echo $text_city_postal_match; ?></td>
              <td><?php echo $city_postal_match; ?></td>
            </tr>
            <?php } ?>
            <?php if ($ship_city_postal_match) { ?>
            <tr>
              <td><?php echo $text_ship_city_postal_match; ?></td>
              <td><?php echo $ship_city_postal_match; ?></td>
            </tr>
            <?php } ?>
            <?php if ($score) { ?>
            <tr>
              <td><?php echo $text_score; ?></td>
              <td><?php echo $score; ?></td>
            </tr>
            <?php } ?>
            <?php if ($explanation) { ?>
            <tr>
              <td><?php echo $text_explanation; ?></td>
              <td><?php echo $explanation; ?></td>
            </tr>
            <?php } ?>
            <?php if ($risk_score) { ?>
            <tr>
              <td><?php echo $text_risk_score; ?></td>
              <td><?php echo $risk_score; ?></td>
            </tr>
            <?php } ?>
            <?php if ($queries_remaining) { ?>
            <tr>
              <td><?php echo $text_queries_remaining; ?></td>
              <td><?php echo $queries_remaining; ?></td>
            </tr>
            <?php } ?>
            <?php if ($maxmind_id) { ?>
            <tr>
              <td><?php echo $text_maxmind_id; ?></td>
              <td><?php echo $maxmind_id; ?></td>
            </tr>
            <?php } ?>
            <?php if ($error) { ?>
            <tr>
              <td><?php echo $text_error; ?></td>
              <td><?php echo $error; ?></td>
            </tr>
            <?php } ?>
          </table>
        </div>
        <?php } ?>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript"><!--
$(document).delegate('#button-invoice', 'click', function() {
	$.ajax({
		url: 'index.php?route=sale/order/createinvoiceno&token=<?php echo $token; ?>&order_id=<?php echo $order_id; ?>',
		dataType: 'json',
		beforeSend: function() {
			$('#button-invoice i').replaceWith('<i class="fa fa-spinner fa-spin"></i>');
			$('#button-invoice').prop('disabled', true);			
		},
		complete: function() {
			$('#button-invoice i').replaceWith('<i class="fa fa-cog"></i>');
			$('#button-invoice').prop('disabled', false);
		},
		success: function(json) {
			$('.alert').remove();
						
			if (json['error']) {
				$('#tab-order').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + '</div>');
			}
			
			if (json['invoice_no']) {
				$('#button-invoice').replaceWith(json['invoice_no']);
			}
		}
	});
});

$(document).delegate('#button-restock', 'click', function() {
	$.ajax({
		url: 'index.php?route=sale/order/restock&token=<?php echo $token; ?>&order_id=<?php echo $order_id; ?>',
		dataType: 'json',
		beforeSend: function() {
			$('#button-restock i').replaceWith('<i class="fa fa-spinner fa-spin"></i>');
			$('#button-restock').prop('disabled', true);			
		},
		complete: function() {
			$('#button-restock i').replaceWith('<i class="fa fa-reply"></i>');
			$('#button-restock').prop('disabled', false);
		},
		success: function(json) {
			$('.alert').remove();
						
			if (json['error']) {
				$('.panel').before('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + '</div>');
			}
			
			if (json['success']) {
                $('.panel').before('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + '</div>');
			}
		}
	});
});

$(document).delegate('#button-reward-add', 'click', function() {
	$.ajax({
		url: 'index.php?route=sale/order/addreward&token=<?php echo $token; ?>&order_id=<?php echo $order_id; ?>',
		type: 'post',
		dataType: 'json',
		beforeSend: function() {
			$('#button-reward-add i').replaceWith('<i class="fa fa-spinner fa-spin"></i>');
			$('#button-reward-add').prop('disabled', true);				
		},
		complete: function() {
			$('#button-reward-add i').replaceWith('<i class="fa fa-plus-circle"></i>');
			$('#button-reward-add').prop('disabled', false);
		},									
		success: function(json) {
			$('.alert').remove();
						
			if (json['error']) {
				$('.panel').before('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + '</div>');
			}
			
			if (json['success']) {
                $('.panel').before('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + '</div>');
				
				$('#button-reward-add').replaceWith('<button id="button-reward-remove" class="btn btn-danger btn-xs"><i class="fa fa-minus-circle"></i> <?php echo $button_reward_remove; ?></button>');
			}
		}
	});
});

$(document).delegate('#button-reward-remove', 'click', function() {
	$.ajax({
		url: 'index.php?route=sale/order/removereward&token=<?php echo $token; ?>&order_id=<?php echo $order_id; ?>',
		type: 'post',
		dataType: 'json',
		beforeSend: function() {
			$('#button-reward-remove i').replaceWith('<i class="fa fa-spinner fa-spin"></i>');
			$('#button-reward-remove').prop('disabled', true);		
		},
		complete: function() {
			$('#button-reward-remove i').replaceWith('<i class="fa fa-minus-circle"></i>');
			$('#button-reward-remove').prop('disabled', false);
		},				
		success: function(json) {
			$('.alert').remove();
						
			if (json['error']) {
				$('.panel').before('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + '</div>');
			}
			
			if (json['success']) {
                $('.panel').before('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + '</div>');
				
				$('#button-reward-remove').replaceWith('<button id="button-reward-add" class="btn btn-success btn-xs"><i class="fa fa-plus-circle"></i> <?php echo $button_reward_add; ?></button>');
			}
		}
	});
});

$(document).delegate('#button-commission-add', 'click', function() {
	$.ajax({
		url: 'index.php?route=sale/order/addcommission&token=<?php echo $token; ?>&order_id=<?php echo $order_id; ?>',
		type: 'post',
		dataType: 'json',
		beforeSend: function() {
			$('#button-commission-add i').replaceWith('<i class="fa fa-spinner fa-spin"></i>');
			$('#button-commission-add').prop('disabled', true);					
		},
		complete: function() {
			$('#button-commission-add i').replaceWith('<i class="fa fa-plus-circle"></i>');
			$('#button-commission-add').prop('disabled', false);
		},			
		success: function(json) {
			$('.alert').remove();
						
			if (json['error']) {
				$('.panel').before('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + '</div>');
			}
			
			if (json['success']) {
                $('.panel').before('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + '</div>');
                
				$('#button-commission-add').replaceWith('<button id="button-commission-remove" class="btn btn-danger btn-xs"><i class="fa fa-minus-circle"></i> <?php echo $button_commission_remove; ?></button>');
			}
		}
	});
});

$(document).delegate('#button-commission-remove', 'click', function() {
	$.ajax({
		url: 'index.php?route=sale/order/removecommission&token=<?php echo $token; ?>&order_id=<?php echo $order_id; ?>',
		type: 'post',
		dataType: 'json',
		beforeSend: function() {
			$('#button-commission-remove i').replaceWith('<i class="fa fa-spinner fa-spin"></i>');
			$('#button-commission-remove').prop('disabled', true);					
		},
		complete: function() {
			$('#button-commission-remove i').replaceWith('<i class="fa fa-minus-circle"></i>');
			$('#button-commission-remove').prop('disabled', false);
		},		
		success: function(json) {
			$('.alert').remove();
						
			if (json['error']) {
				$('.panel').before('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + '</div>');
			}
			
			if (json['success']) {
                $('.panel').before('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + '</div>');
				
				$('#button-commission-remove').replaceWith('<button id="button-commission-add" class="btn btn-success btn-xs"><i class="fa fa-minus-circle"></i> <?php echo $button_commission_add; ?></button>');
			}
		}
	});
});

$('#history').delegate('.pagination a', 'click', function(e) {
	e.preventDefault();
	
	$('#history').load(this.href);
});			

$('#history').load('index.php?route=sale/order/history&token=<?php echo $token; ?>&order_id=<?php echo $order_id; ?>');

$('#button-history').on('click', function() {
	$.ajax({
		url: 'index.php?route=sale/order/history&token=<?php echo $token; ?>&order_id=<?php echo $order_id; ?>',
		type: 'post',
		dataType: 'html',
		data: 'order_status_id=' + encodeURIComponent($('select[name=\'order_status_id\']').val()) + '&notify=' + ($('input[name=\'notify\']').prop('checked') ? 1 : 0) + '&append=' + ($('input[name=\'append\']').prop('checked') ? 1 : 0) + '&comment=' + encodeURIComponent($('textarea[name=\'comment\']').val()),
		beforeSend: function() {
			$('#button-history i').replaceWith('<i class="fa fa-spinner fa-spin"></i>');
			$('#button-history').prop('disabled', true);				
		},
		complete: function() {
			$('#button-history i').replaceWith('<i class="fa fa-plus-circle"></i>');
			$('#button-history').prop('disabled', false);
		},
		success: function(html) {
			$('.alert').remove();
			
			$('#history').html(html);
			
			$('textarea[name=\'comment\']').val('');
			
			$('#order-status').html($('select[name=\'order_status_id\'] option:selected').text());
		}
	});
});
//--></script> 
<?php echo $footer; ?> 