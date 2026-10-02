jQuery(function($){
  function bindSearch(el){
    $(el).autocomplete({minLength:2,delay:250,source:function(req,res){$.getJSON(XP_TI_ADMIN.ajaxurl,{action:'xp_ti_search_listings',nonce:XP_TI_ADMIN.nonce,q:req.term},function(data){res($.map(data,function(x){return {label:x.title,value:x.title,id:x.id};}));});},select:function(e,ui){var row=$(this).closest('.xp-ti-item');row.find('.xp-ti-listing-id').val(ui.item.id);$(this).val(ui.item.value);return false;}});
  }
  $('.xp-ti-listing-search').each(function(){bindSearch(this);});
  $('#xp-ti-builder').on('click','.xp-ti-add-item',function(){var day=$(this).closest('.xp-ti-day'), list=day.find('.xp-ti-items'), d=day.data('day'), i=list.children().length;var html='<div class="xp-ti-item" draggable="true"><span class="xp-ti-handle">☷</span><input type="time" name="xp_ti_days['+d+'][items]['+i+'][time]"><input type="text" name="xp_ti_days['+d+'][items]['+i+'][activity]" placeholder="Activity (e.g. Breakfast)"><input class="xp-ti-listing-id" type="hidden" name="xp_ti_days['+d+'][items]['+i+'][listing_id]"><input class="xp-ti-listing-search" type="text" placeholder="Search existing listing…" autocomplete="off"><input type="number" min="0" name="xp_ti_days['+d+'][items]['+i+'][duration]" placeholder="Minutes"><input type="text" name="xp_ti_days['+d+'][items]['+i+'][notes]" placeholder="Notes"><button type="button" class="button-link-delete xp-ti-remove">Remove</button></div>';list.append(html);bindSearch(list.children().last().find('.xp-ti-listing-search'));});
  $('#xp-ti-builder').on('click','.xp-ti-remove',function(){$(this).closest('.xp-ti-item').remove();});
  $('.xp-ti-items').sortable({handle:'.xp-ti-handle',placeholder:'xp-ti-placeholder'});
});
