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
        <button id="button-send" class="btn btn-primary" onclick="send('index.php?route=marketing/contact/send&token=<?php echo $token; ?>');"><i class="fa fa-envelope"></i> <?php echo $button_send; ?></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn"><i class="fa fa-reply"></i></a></div>
      <h1 class="panel-title"><i class="fa fa-envelope fa-lg"></i> <?php echo $heading_title; ?></h1>
    </div>
    <div class="panel-body">
      <form class="form-horizontal">
        <div class="form-group">
          <label class="col-sm-2 control-label" for="input-store"><?php echo $entry_store; ?></label>
          <div class="col-sm-10">
            <select name="store_id" id="input-store" class="form-control">
              <option value="0"><?php echo $text_default; ?></option>
              <?php foreach ($stores as $store) { ?>
              <option value="<?php echo $store['store_id']; ?>"><?php echo $store['name']; ?></option>
              <?php } ?>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-2 control-label" for="input-to"><?php echo $entry_to; ?></label>
          <div class="col-sm-10">
            <select name="to" id="input-to" class="form-control">
              <option value="newsletter"><?php echo $text_newsletter; ?></option>
              <option value="customer_all"><?php echo $text_customer_all; ?></option>
              <option value="customer_group"><?php echo $text_customer_group; ?></option>
              <option value="customer"><?php echo $text_customer; ?></option>
              <option value="affiliate_all"><?php echo $text_affiliate_all; ?></option>
              <option value="affiliate"><?php echo $text_affiliate; ?></option>
              <option value="product"><?php echo $text_product; ?></option>
            </select>
          </div>
        </div>
        <div class="form-group to" id="to-customer-group">
          <label class="col-sm-2 control-label" for="input-customer-group"><?php echo $entry_customer_group; ?></label>
          <div class="col-sm-10">
            <select name="customer_group_id" id="input-customer-group" class="form-control">
              <?php foreach ($customer_groups as $customer_group) { ?>
              <option value="<?php echo $customer_group['customer_group_id']; ?>"><?php echo $customer_group['name']; ?></option>
              <?php } ?>
            </select>
          </div>
        </div>
        <div class="form-group to" id="to-customer">
          <label class="col-sm-2 control-label" for="input-customer"><?php echo $entry_customer; ?></label>
          <div class="col-sm-10">
            <input type="text" name="customers" value="" placeholder="<?php echo $entry_customer; ?>" id="input-customer" class="form-control" />
            <span class="help-block"><?php echo $help_customer; ?></span>
            <div id="customer" class="well well-sm" style="height: 150px; overflow: auto;"></div>
          </div>
        </div>
        <div class="form-group to" id="to-affiliate">
          <label class="col-sm-2 control-label" for="input-affiliate"><?php echo $entry_affiliate; ?></label>
          <div class="col-sm-10">
            <input type="text" name="affiliates" value="" placeholder="<?php echo $entry_affiliate; ?>" id="input-affiliate" class="form-control" />
            <span class="help-block"><?php echo $help_affiliate; ?></span>
            <div id="affiliate" class="well well-sm" style="height: 150px; overflow: auto;"></div>
          </div>
        </div>
        <div class="form-group to" id="to-product">
          <label class="col-sm-2 control-label" for="input-product"><?php echo $entry_product; ?></label>
          <div class="col-sm-10">
            <input type="text" name="products" value="" placeholder="<?php echo $entry_product; ?>" id="input-product" class="form-control" />
            <span class="help-block"><?php echo $help_product; ?></span>
            <div id="product" class="well well-sm" style="height: 150px; overflow: auto;"></div>
          </div>
        </div>
        <div class="form-group required">
          <label class="col-sm-2 control-label" for="input-subject"><?php echo $entry_subject; ?></label>
          <div class="col-sm-10">
            <input type="text" name="subject" value="" placeholder="<?php echo $entry_subject; ?>" id="input-subject" class="form-control" />
          </div>
        </div>
        <div class="form-group required">
          <label class="col-sm-2 control-label" for="input-message"><?php echo $entry_message; ?></label>
          <div class="col-sm-10">
            <textarea name="message" placeholder="<?php echo $entry_message; ?>" id="input-message" class="form-control"></textarea>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
<script type="text/javascript" src="view/javascript/ckeditor/ckeditor.js"></script> 
<script type="text/javascript"><!--
CKEDITOR.replace('input-message');
//--></script> 
<script type="text/javascript"><!--	
$('select[name=\'to\']').on('change', function() {
	$('.to').hide();
	
	$('#to-' + this.value.replace('_', '-')).show();
});

$('select[name=\'to\']').trigger('change');
//--></script> 
<script type="text/javascript"><!--
// Customers
$('input[name=\'customers\']').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=sale/customer/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
			dataType: 'json',			
			success: function(json) {
				response($.map(json, function(item) {
					return {
						label: item['name'],
						value: item['customer_id']
					}
				}));
			}
		});
	},
	'select': function(item) {
		$('input[name=\'customers\']').val('');
		
		$('#customer' + item['value']).remove();
		
		$('#customer').append('<div id="customer' + item['value'] + '"><i class="fa fa-minus-circle"></i> ' + item['label'] + '<input type="hidden" name="customer[]" value="' + item['value'] + '" /></div>');	
	}	
});

$('#customer').delegate('.fa-minus-circle', 'click', function() {
	$(this).parent().remove();
});

// Affiliates
$('input[name=\'affiliates\']').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=sale/customer/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
			dataType: 'json',			
			success: function(json) {
				response($.map(json, function(item) {
					return {
						label: item['name'],
						value: item['customer_id']
					}
				}));
			}
		});
	},
	'select': function(item) {
		$('input[name=\'affiliates\']').val('');
		
		$('#affiliate' + item['value']).remove();
		
		$('#affiliate').append('<div id="affiliate' + item['value'] + '"><i class="fa fa-minus-circle"></i> ' + item['label'] + '<input type="hidden" name="affiliate[]" value="' + item['value'] + '" /></div>');	
	}	
});

$('#affiliate').delegate('.fa-minus-circle', 'click', function() {
	$(this).parent().remove();
});

// Products
$('input[name=\'products\']').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=catalog/product/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
			dataType: 'json',			
			success: function(json) {
				response($.map(json, function(item) {
					return {
						label: item['name'],
						value: item['product_id']
					}
				}));
			}
		});
	},
	'select': function(item) {
		$('input[name=\'products\']').val('');
		
		$('#product' + item['value']).remove();
		
		$('#product').append('<div id="product' + item['value'] + '"><i class="fa fa-minus-circle"></i> ' + item['label'] + '<input type="hidden" name="product[]" value="' + item['value'] + '" /></div>');	
	}	
});

$('#product').delegate('.fa-minus-circle', 'click', function() {
	$(this).parent().remove();
});

function send(url) {
	$('textarea[name=\'message\']').html(CKEDITOR.instances['input-message'].getData());
	
	$.ajax({
		url: url,
		type: 'post',
		data: $('select, input, textarea'),		
		dataType: 'json',
		beforeSend: function() {
			$('#button-send i').replaceWith('<i class="fa fa-spinner fa-spin"></i>');
			$('#button-send').prop('disabled', true);
		},
		complete: function() {
			$('#button-send i').replaceWith('<i class="fa fa-envelope"></i>');
			$('#button-send').prop('disabled', false);
		},				
		success: function(json) {
			$('.alert, .error').remove();
			
			if (json['error']) {
				if (json['error']['warning']) {
					$('.panel').before('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error']['warning'] + '</div>');
				}
				
				if (json['error']['subject']) {
					$('input[name=\'subject\']').after('<span class="text-danger">' + json['error']['subject'] + '</span>');
				}	
				
				if (json['error']['message']) {
					$('textarea[name=\'message\']').parent().append('<span class="text-danger">' + json['error']['message'] + '</span>');
				}									
			}			
			
			if (json['next']) {
				if (json['success']) {
					$('.panel').before('<div class="alert alert-success"><i class="fa fa-check-circle"></i>  ' + json['success'] + '</div>');
					
					send(json['next']);
				}		
			} else {
				if (json['success']) {
					$('.panel').before('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + '</div>');
				}					
			}				
		}
	});
}
//--></script> 
<?php echo $footer; ?>