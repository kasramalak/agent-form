<?php
global $post, $current_user, $ele_settings;
$return_array = houzez20_property_contact_form(false);
if(empty($return_array)) {
    return;
}
$hide_form_fields = houzez_option('hide_prop_contact_form_fields');
$terms_page_id = houzez_option('terms_condition');
$gdpr_checkbox = houzez_option('gdpr_hide_checkbox', 1);
$terms_page_id = apply_filters('wpml_object_id', $terms_page_id, 'page', true);

$agent_display = houzez_get_listing_data('agent_display_option');
$property_id = houzez_get_listing_data('property_id');

$user_name = $user_email = '';
if(!houzez_is_admin()) {
    $user_name =  $current_user->display_name;
    $user_email =  $current_user->user_email;
}
$agent_email = is_email($return_array['agent_email']);

$action_class = "houzez-send-message";
$login_class = '';
$dataModel = '';
if(!is_user_logged_in()) {
    $action_class = '';
    $login_class = 'msg-login-required';
    $dataModel = 'data-toggle="modal" data-target="#login-register-form"';
}

$section_header = isset($ele_settings['section_header']) ? $ele_settings['section_header'] : true;
$section_title = isset($ele_settings['section_title']) && !empty($ele_settings['section_title']) ? $ele_settings['section_title'] : houzez_option('sps_contact_info', 'Contact Information');

if($agent_display != 'none') {
?>
<div class="property-contact-agent-wrap property-section-wrap shadow-sm p-4 rounded-4 bg-white" id="property-contact-agent-wrap">
    <div class="block-wrap">
        <?php if($section_header) { ?>
        <div class="block-title-wrap d-flex justify-content-between align-items-center mb-3">
            <h2><?php echo esc_attr($section_title); ?></h2>
            <?php if($return_array['is_single_agent'] == true && houzez_option('agent_view_listing')) : ?>
            <a class="btn btn-primary btn-slim" href="<?php echo esc_url($return_array['link']); ?>" target="_blank">
                <?php echo houzez_option('spl_con_view_listings', 'View listings'); ?>
            </a>
            <?php endif; ?>
        </div>
        <?php } ?>

        <div class="block-content-wrap">
            <?php if(houzez_form_type()) {
                echo $return_array['agent_data']; ?>
                <div class="block-title-wrap">
                    <h3><?php echo houzez_option('sps_propperty_enqry', 'Inquire About This Property'); ?></h3>
                </div>
                <?php if(!empty(houzez_option('contact_form_agent_bottom'))) {
                    echo do_shortcode(houzez_option('contact_form_agent_bottom'));
                }
            } else { ?>
<style>
.agent-form-modern .form-group {
  position: relative;
  margin-bottom: 1.5rem;
}
.agent-form-modern .form-control {
  background: #fff;
  border-radius: 1rem;
  border: 1px solid #e5e7eb;
  padding: 1.1rem 1.25rem 0.6rem 1.25rem;
  font-size: 1rem;
  box-shadow: none;
  transition: border-color 0.2s, box-shadow 0.2s;
}
.agent-form-modern .form-control:focus {
  border-color: #2563eb;
  box-shadow: 0 4px 16px rgba(37,99,235,0.08);
}
.agent-form-modern label.floating-label {
  position: absolute;
  left: 1.25rem;
  top: 1.1rem;
  color: #9ca3af;
  font-size: 1rem;
  pointer-events: none;
  background: transparent;
  transition: all 0.2s;
}
.agent-form-modern .form-control:focus + label.floating-label,
.agent-form-modern .form-control:not(:placeholder-shown) + label.floating-label {
  top: -0.7rem;
  left: 1rem;
  font-size: 0.85rem;
  color: #2563eb;
  background: #fff;
  padding: 0 0.25rem;
}
.agent-form-modern .btn-modern {
  border-radius: 1rem;
  font-weight: 600;
  font-size: 1.1rem;
  padding: 0.9rem 1.5rem;
  box-shadow: 0 2px 8px rgba(0,0,0,0.04);
  transition: box-shadow 0.2s, transform 0.2s;
}
.agent-form-modern .btn-modern:focus,
.agent-form-modern .btn-modern:hover {
  box-shadow: 0 6px 24px rgba(37,99,235,0.10);
  transform: translateY(-2px);
}
.agent-form-modern .form-multi-group {
  display: flex;
  gap: 1rem;
}
.agent-form-modern .form-multi-group .form-control {
  flex: 1 1 0;
}
.agent-form-modern .form-multi-group select {
  min-width: 110px;
}
.agent-form-modern .form-container {
  background: #fff;
  border-radius: 1.5rem;
  box-shadow: 0 4px 32px rgba(0,0,0,0.07);
  padding: 2rem 1.5rem;
  max-width: 100%;
}
</style>
<div class="form-container agent-form-modern">
  <form method="post" action="#">
    <?php echo $return_array['agent_data']; ?>
    <div class="row">
      <?php if($hide_form_fields['name'] != 1) { ?>
      <div class="col-md-6">
        <div class="form-group">
          <input class="form-control" name="name" placeholder=" " type="text" autocomplete="off">
          <label class="floating-label">Name</label>
        </div>
      </div>
      <?php } ?>
      <?php if($hide_form_fields['phone'] != 1) { ?>
      <div class="col-md-6">
        <div class="form-group form-multi-group">
          <select name="country_code" class="form-control" style="max-width: 120px;">
            <option value="">Code</option>
            <!-- Middle East -->
            <optgroup label="Middle East">
                <option value="+971">+971 (UAE)</option>
                <option value="+966">+966 (KSA)</option>
                <option value="+974">+974 (Qatar)</option>
                <option value="+968">+968 (Oman)</option>
                <option value="+973">+973 (Bahrain)</option>
                <option value="+965">+965 (Kuwait)</option>
                <option value="+98">+98 (Iran)</option>
                <option value="+962">+962 (Jordan)</option>
                <option value="+963">+963 (Syria)</option>
                <option value="+961">+961 (Lebanon)</option>
                <option value="+964">+964 (Iraq)</option>
                <option value="+967">+967 (Yemen)</option>
                <option value="+20">+20 (Egypt)</option>
                <option value="+218">+218 (Libya)</option>
                <option value="+249">+249 (Sudan)</option>
                <option value="+253">+253 (Djibouti)</option>
                <option value="+252">+252 (Somalia)</option>
            </optgroup>
            <optgroup label="Africa">
                <option value="+212">+212 (Morocco)</option>
                <option value="+216">+216 (Tunisia)</option>
                <option value="+213">+213 (Algeria)</option>
                <option value="+27">+27 (South Africa)</option>
                <option value="+234">+234 (Nigeria)</option>
                <option value="+254">+254 (Kenya)</option>
                <option value="+251">+251 (Ethiopia)</option>
                <option value="+255">+255 (Tanzania)</option>
                <option value="+233">+233 (Ghana)</option>
                <option value="+221">+221 (Senegal)</option>
                <option value="+224">+224 (Guinea)</option>
                <option value="+226">+226 (Burkina Faso)</option>
                <option value="+227">+227 (Niger)</option>
                <option value="+228">+228 (Togo)</option>
                <option value="+229">+229 (Benin)</option>
                <option value="+230">+230 (Mauritius)</option>
                <option value="+231">+231 (Liberia)</option>
                <option value="+232">+232 (Sierra Leone)</option>
                <option value="+235">+235 (Chad)</option>
                <option value="+236">+236 (CAR)</option>
                <option value="+237">+237 (Cameroon)</option>
                <option value="+238">+238 (Cape Verde)</option>
                <option value="+239">+239 (Sao Tome)</option>
                <option value="+240">+240 (Eq. Guinea)</option>
                <option value="+241">+241 (Gabon)</option>
                <option value="+242">+242 (Congo)</option>
                <option value="+243">+243 (DRC)</option>
                <option value="+244">+244 (Angola)</option>
                <option value="+245">+245 (Guinea-Bissau)</option>
                <option value="+246">+246 (Diego Garcia)</option>
                <option value="+247">+247 (Ascension)</option>
                <option value="+248">+248 (Seychelles)</option>
                <option value="+250">+250 (Rwanda)</option>
                <option value="+256">+256 (Uganda)</option>
                <option value="+257">+257 (Burundi)</option>
                <option value="+258">+258 (Mozambique)</option>
                <option value="+260">+260 (Zambia)</option>
                <option value="+261">+261 (Madagascar)</option>
                <option value="+262">+262 (Réunion)</option>
                <option value="+263">+263 (Zimbabwe)</option>
                <option value="+264">+264 (Namibia)</option>
                <option value="+265">+265 (Malawi)</option>
                <option value="+266">+266 (Lesotho)</option>
                <option value="+267">+267 (Botswana)</option>
                <option value="+268">+268 (Swaziland)</option>
                <option value="+269">+269 (Comoros)</option>
                <option value="+290">+290 (St Helena)</option>
                <option value="+291">+291 (Eritrea)</option>
                <option value="+297">+297 (Aruba)</option>
                <option value="+298">+298 (Faroe Islands)</option>
                <option value="+299">+299 (Greenland)</option>
            </optgroup>
            <optgroup label="Asia">
                <option value="+7">+7 (Russia)</option>
                <option value="+60">+60 (Malaysia)</option>
                <option value="+61">+61 (Australia)</option>
                <option value="+62">+62 (Indonesia)</option>
                <option value="+63">+63 (Philippines)</option>
                <option value="+64">+64 (New Zealand)</option>
                <option value="+65">+65 (Singapore)</option>
                <option value="+66">+66 (Thailand)</option>
                <option value="+81">+81 (Japan)</option>
                <option value="+82">+82 (South Korea)</option>
                <option value="+84">+84 (Vietnam)</option>
                <option value="+86">+86 (China)</option>
                <option value="+90">+90 (Turkey)</option>
                <option value="+91">+91 (India)</option>
                <option value="+92">+92 (Pakistan)</option>
                <option value="+93">+93 (Afghanistan)</option>
                <option value="+94">+94 (Sri Lanka)</option>
                <option value="+95">+95 (Myanmar)</option>
                <option value="+673">+673 (Brunei)</option>
                <option value="+880">+880 (Bangladesh)</option>
                <option value="+960">+960 (Maldives)</option>
                <option value="+970">+970 (Palestine)</option>
                <option value="+972">+972 (Israel)</option>
                <option value="+975">+975 (Bhutan)</option>
                <option value="+976">+976 (Mongolia)</option>
                <option value="+977">+977 (Nepal)</option>
                <option value="+992">+992 (Tajikistan)</option>
                <option value="+993">+993 (Turkmenistan)</option>
                <option value="+994">+994 (Azerbaijan)</option>
                <option value="+995">+995 (Georgia)</option>
                <option value="+996">+996 (Kyrgyzstan)</option>
                <option value="+998">+998 (Uzbekistan)</option>
            </optgroup>
            <optgroup label="Europe">
                <option value="+30">+30 (Greece)</option>
                <option value="+31">+31 (Netherlands)</option>
                <option value="+32">+32 (Belgium)</option>
                <option value="+33">+33 (France)</option>
                <option value="+34">+34 (Spain)</option>
                <option value="+36">+36 (Hungary)</option>
                <option value="+39">+39 (Italy)</option>
                <option value="+40">+40 (Romania)</option>
                <option value="+41">+41 (Switzerland)</option>
                <option value="+43">+43 (Austria)</option>
                <option value="+44">+44 (UK)</option>
                <option value="+45">+45 (Denmark)</option>
                <option value="+46">+46 (Sweden)</option>
                <option value="+47">+47 (Norway)</option>
                <option value="+48">+48 (Poland)</option>
                <option value="+49">+49 (Germany)</option>
                <option value="+350">+350 (Gibraltar)</option>
                <option value="+351">+351 (Portugal)</option>
                <option value="+352">+352 (Luxembourg)</option>
                <option value="+353">+353 (Ireland)</option>
                <option value="+354">+354 (Iceland)</option>
                <option value="+355">+355 (Albania)</option>
                <option value="+356">+356 (Malta)</option>
                <option value="+357">+357 (Cyprus)</option>
                <option value="+358">+358 (Finland)</option>
                <option value="+359">+359 (Bulgaria)</option>
                <option value="+370">+370 (Lithuania)</option>
                <option value="+371">+371 (Latvia)</option>
                <option value="+372">+372 (Estonia)</option>
                <option value="+373">+373 (Moldova)</option>
                <option value="+374">+374 (Armenia)</option>
                <option value="+375">+375 (Belarus)</option>
                <option value="+376">+376 (Andorra)</option>
                <option value="+377">+377 (Monaco)</option>
                <option value="+378">+378 (San Marino)</option>
                <option value="+379">+379 (Vatican)</option>
                <option value="+380">+380 (Ukraine)</option>
                <option value="+381">+381 (Serbia)</option>
                <option value="+382">+382 (Montenegro)</option>
                <option value="+383">+383 (Kosovo)</option>
                <option value="+385">+385 (Croatia)</option>
                <option value="+386">+386 (Slovenia)</option>
                <option value="+387">+387 (Bosnia)</option>
                <option value="+389">+389 (Macedonia)</option>
                <option value="+420">+420 (Czech)</option>
                <option value="+421">+421 (Slovakia)</option>
                <option value="+423">+423 (Liechtenstein)</option>
            </optgroup>
            <optgroup label="North America">
                <option value="+1">+1 (USA/Canada)</option>
                <option value="+1242">+1242 (Bahamas)</option>
                <option value="+1246">+1246 (Barbados)</option>
                <option value="+1264">+1264 (Anguilla)</option>
                <option value="+1268">+1268 (Antigua)</option>
                <option value="+1284">+1284 (BVI)</option>
                <option value="+1340">+1340 (USVI)</option>
                <option value="+1441">+1441 (Bermuda)</option>
                <option value="+1473">+1473 (Grenada)</option>
                <option value="+1649">+1649 (Turks & Caicos)</option>
                <option value="+1664">+1664 (Montserrat)</option>
                <option value="+1670">+1670 (N Mariana)</option>
                <option value="+1671">+1671 (Guam)</option>
                <option value="+1684">+1684 (American Samoa)</option>
                <option value="+1758">+1758 (St Lucia)</option>
                <option value="+1767">+1767 (Dominica)</option>
                <option value="+1784">+1784 (St Vincent)</option>
                <option value="+1809">+1809 (Dominican Rep.)</option>
                <option value="+1868">+1868 (Trinidad)</option>
                <option value="+1869">+1869 (St Kitts)</option>
                <option value="+1876">+1876 (Jamaica)</option>
                <option value="+1939">+1939 (Puerto Rico)</option>
            </optgroup>
            <optgroup label="Central/South America">
                <option value="+501">+501 (Belize)</option>
                <option value="+502">+502 (Guatemala)</option>
                <option value="+503">+503 (El Salvador)</option>
                <option value="+504">+504 (Honduras)</option>
                <option value="+505">+505 (Nicaragua)</option>
                <option value="+506">+506 (Costa Rica)</option>
                <option value="+507">+507 (Panama)</option>
                <option value="+509">+509 (Haiti)</option>
                <option value="+51">+51 (Peru)</option>
                <option value="+52">+52 (Mexico)</option>
                <option value="+53">+53 (Cuba)</option>
                <option value="+54">+54 (Argentina)</option>
                <option value="+55">+55 (Brazil)</option>
                <option value="+56">+56 (Chile)</option>
                <option value="+57">+57 (Colombia)</option>
                <option value="+58">+58 (Venezuela)</option>
                <option value="+591">+591 (Bolivia)</option>
                <option value="+592">+592 (Guyana)</option>
                <option value="+593">+593 (Ecuador)</option>
                <option value="+594">+594 (French Guiana)</option>
                <option value="+595">+595 (Paraguay)</option>
                <option value="+596">+596 (Martinique)</option>
                <option value="+597">+597 (Suriname)</option>
                <option value="+598">+598 (Uruguay)</option>
            </optgroup>
            <optgroup label="Oceania">
                <option value="+61">+61 (Australia)</option>
                <option value="+64">+64 (New Zealand)</option>
                <option value="+672">+672 (Norfolk Island)</option>
                <option value="+675">+675 (Papua New Guinea)</option>
                <option value="+676">+676 (Tonga)</option>
                <option value="+677">+677 (Solomon Is.)</option>
                <option value="+678">+678 (Vanuatu)</option>
                <option value="+679">+679 (Fiji)</option>
                <option value="+680">+680 (Palau)</option>
                <option value="+681">+681 (Wallis Futuna)</option>
                <option value="+682">+682 (Cook Is.)</option>
                <option value="+683">+683 (Niue)</option>
                <option value="+685">+685 (Samoa)</option>
                <option value="+686">+686 (Kiribati)</option>
                <option value="+687">+687 (New Caledonia)</option>
                <option value="+688">+688 (Tuvalu)</option>
                <option value="+689">+689 (French Polynesia)</option>
                <option value="+690">+690 (Tokelau)</option>
                <option value="+691">+691 (Micronesia)</option>
                <option value="+692">+692 (Marshall Is.)</option>
            </optgroup>
       </select>
            <input class="form-control" name="phone" placeholder=" " type="text" autocomplete="off">
            <label class="floating-label" style="left: 140px;">Phone</label>
        </div>
      </div>
      <?php } ?>

      <?php if($hide_form_fields['email'] != 1) { ?>
      <div class="col-md-6">
        <div class="form-group">
          <input class="form-control" name="email" placeholder=" " type="email" autocomplete="off">
          <label class="floating-label">Email</label>
        </div>
      </div>
      <?php } ?>

      <?php if($hide_form_fields['usertype'] != 1) { ?>
      <div class="col-md-6">
        <div class="form-group">
          <select name="user_type" class="form-control" title="Select">
            <option value="">Select</option>
            <?php if(houzez_option('spl_con_buyer') != "") { ?>
            <option value="buyer"><?php echo houzez_option('spl_con_buyer', "I'm a buyer"); ?></option>
            <?php } ?>
            <?php if(houzez_option('spl_con_agent') != "") { ?>
            <option value="agent"><?php echo houzez_option('spl_con_agent', "I'm an agent"); ?></option>
            <?php } ?>
            <?php if(houzez_option('spl_con_other') != "") { ?>
            <option value="other"><?php echo houzez_option('spl_con_other', 'Other'); ?></option>
            <?php } ?>
          </select>
          <label class="floating-label">I'm a</label>
        </div>
      </div>
      <?php } ?>

      <?php if($hide_form_fields['message'] != 1) { ?>
      <div class="col-12">
        <div class="form-group">
          <textarea class="form-control" name="message" rows="5" placeholder=" " autocomplete="off"><?php echo houzez_option('spl_con_interested', "Hello, I am interested in"); ?> [<?php echo get_the_title(); ?>]</textarea>
          <label class="floating-label">Message</label>
        </div>
      </div>
      <?php } ?>
    </div>
    <?php if (houzez_option('gdpr_and_terms_checkbox', 1)): ?>
    <div class="form-group mb-2">
      <label class="control control--checkbox m-0 hz-terms-of-use">
        <?php if (!$gdpr_checkbox): ?>
        <input type="checkbox" name="privacy_policy" checked>
        <span class="control__indicator"></span>
        <?php endif; ?>
        <div class="gdpr-text-wrap">
          By submitting this form I agree to
          <a target="_blank" href="<?php echo esc_url(get_permalink($terms_page_id)); ?>">Terms of Use</a>
        </div>
      </label>
    </div>
    <?php endif; ?>
    <input type="hidden" name="property_agent_contact_security" value="<?php echo wp_create_nonce('property_agent_contact_nonce'); ?>">
    <input type="hidden" name="property_permalink" value="<?php echo esc_url(get_permalink($post->ID)); ?>">
    <input type="hidden" name="property_title" value="<?php echo esc_attr(get_the_title($post->ID)); ?>">
    <input type="hidden" name="property_id" value="<?php echo esc_attr($property_id); ?>">
    <input type="hidden" name="action" value="houzez_property_agent_contact">
    <input type="hidden" name="listing_id" value="<?php echo intval($post->ID); ?>">
    <input type="hidden" name="is_listing_form" value="yes">
    <input type="hidden" name="agent_id" value="<?php echo intval($return_array['agent_id']); ?>">
    <input type="hidden" name="agent_type" value="<?php echo esc_attr($return_array['agent_type']); ?>">
    <div class="d-flex gap-3">
      <button type="submit" class="btn btn-primary btn-modern flex-fill"><i class="fas fa-paper-plane me-2"></i>Send Message</button>
      <?php if ($return_array['is_single_agent'] && !empty($agent_number) && $agent_mobile_num && !wp_is_mobile()): ?>
      <a href="tel:<?php echo esc_attr($agent_mobile_call); ?>" class="btn btn-outline-primary btn-modern flex-fill"><i class="fas fa-phone-alt me-2"></i>Call</a>
      <?php endif; ?>
    </div>
    <?php if ($return_array['is_single_agent'] && !empty($agent_whatsapp_call) && $agent_whatsapp_num): ?>
    <a target="_blank" href="https://api.whatsapp.com/send?phone=<?php echo esc_attr($agent_whatsapp_call); ?>&text=<?php echo houzez_option('spl_con_interested', "Hello, I am interested in").' ['.get_the_title().'] '.get_permalink(); ?>" class="btn btn-success btn-modern w-100 mt-3"><i class="fab fa-whatsapp me-2"></i>WhatsApp</a>
    <?php endif; ?>
  </form>
</div>
            <?php } ?>
        </div>
    </div>
</div>
<?php } ?>
