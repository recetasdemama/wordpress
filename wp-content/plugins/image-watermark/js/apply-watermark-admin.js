jQuery(document).ready(function($) {
	jQuery('<option>').val('applywatermark').text(watermark_args.apply_watermark).appendTo("select[name='action']");
	jQuery('<option>').val('applywatermark').text(watermark_args.apply_watermark).appendTo("select[name='action2']");
});