<?php
global $post, $current_user, $ele_settings;
$return_array = houzez20_property_contact_form();
if (empty($return_array)) return;

$agent_info = isset($ele_settings['agent_detail']) ? $ele_settings['agent_detail'] : 'yes';
$terms_page_id = apply_filters('wpml_object_id', houzez_option('terms_condition'), 'page', true);
$hide_form_fields = houzez_option('hide_prop_contact_form_fields');
$gdpr_checkbox = houzez_option('gdpr_hide_checkbox', 1);
$agent_display = houzez_get_listing_data('agent_display_option');
$property_id = houzez_get_listing_data('property_id');

$agent_number = $return_array['agent_mobile'] ?: $return_array['agent_phone'];
$agent_mobile_call = $return_array['agent_mobile_call'] ?: $return_array['agent_phone_call'];
$agent_whatsapp_call = $return_array['agent_whatsapp_call'];

$user_name = $user_email = '';
if (!houzez_is_admin()) {
    $user_name = $current_user->display_name;
    $user_email = $current_user->user_email;
}

$send_btn_class = 'btn-full-width';

$action_class = is_user_logged_in() ? "houzez-send-message" : '';
$login_class = is_user_logged_in() ? '' : 'msg-login-required';
$dataModel = is_user_logged_in() ? '' : 'data-toggle="modal" data-target="#login-register-form"';

$agent_email = is_email($return_array['agent_email']);
$agent_mobile_num = houzez_option('agent_mobile_num', 1);
$agent_whatsapp_num = houzez_option('agent_whatsapp_num', 1);

$whatsappBtnClass = "hz-btn-whatsapp btn-full-width mt-10";
$messageBtnClass = "btn-full-width mt-10";

if ($agent_email && $agent_display != 'none'):
?>

<div class="property-form-wrap">
  <div class="property-form clearfix">
    <form method="post" action="#">
      <?php if ($agent_info == 'yes') echo $return_array['agent_data']; ?>

      <?php if ($hide_form_fields['name'] != 1): ?>
      <div class="form-group mb-2">
        <input class="form-control" name="name" value="<?php echo esc_attr($user_name); ?>" type="text" placeholder="<?php echo houzez_option('spl_con_name', 'Name'); ?>">
      </div>
      <?php endif; ?>

      <?php if ($hide_form_fields['phone'] != 1): ?>
      <div class="form-group mb-2 d-flex gap-2" style="display: flex; gap: 10px;">
        <select name="country_code_bottom_unique" class="form-control w-100" style="max-width: 120px;">
          <option value="">Code</option>
          <option value="+93">+93 Afghanistan</option>
          <option value="+355">+355 Albania</option>
          <option value="+213">+213 Algeria</option>
          <option value="+1-684">+1-684 American Samoa</option>
          <option value="+376">+376 Andorra</option>
          <option value="+244">+244 Angola</option>
          <option value="+1-264">+1-264 Anguilla</option>
          <option value="+672">+672 Antarctica</option>
          <option value="+1-268">+1-268 Antigua and Barbuda</option>
          <option value="+54">+54 Argentina</option>
          <option value="+374">+374 Armenia</option>
          <option value="+297">+297 Aruba</option>
          <option value="+61">+61 Australia</option>
          <option value="+43">+43 Austria</option>
          <option value="+994">+994 Azerbaijan</option>
          <option value="+1-242">+1-242 Bahamas</option>
          <option value="+973">+973 Bahrain</option>
          <option value="+880">+880 Bangladesh</option>
          <option value="+1-246">+1-246 Barbados</option>
          <option value="+375">+375 Belarus</option>
          <option value="+32">+32 Belgium</option>
          <option value="+501">+501 Belize</option>
          <option value="+229">+229 Benin</option>
          <option value="+1-441">+1-441 Bermuda</option>
          <option value="+975">+975 Bhutan</option>
          <option value="+591">+591 Bolivia</option>
          <option value="+387">+387 Bosnia and Herzegovina</option>
          <option value="+267">+267 Botswana</option>
          <option value="+55">+55 Brazil</option>
          <option value="+246">+246 British Indian Ocean Territory</option>
          <option value="+1-284">+1-284 British Virgin Islands</option>
          <option value="+673">+673 Brunei</option>
          <option value="+359">+359 Bulgaria</option>
          <option value="+226">+226 Burkina Faso</option>
          <option value="+257">+257 Burundi</option>
          <option value="+855">+855 Cambodia</option>
          <option value="+237">+237 Cameroon</option>
          <option value="+1">+1 Canada</option>
          <option value="+238">+238 Cape Verde</option>
          <option value="+1-345">+1-345 Cayman Islands</option>
          <option value="+236">+236 Central African Republic</option>
          <option value="+235">+235 Chad</option>
          <option value="+56">+56 Chile</option>
          <option value="+86">+86 China</option>
          <option value="+61">+61 Christmas Island</option>
          <option value="+61">+61 Cocos Islands</option>
          <option value="+57">+57 Colombia</option>
          <option value="+269">+269 Comoros</option>
          <option value="+682">+682 Cook Islands</option>
          <option value="+506">+506 Costa Rica</option>
          <option value="+385">+385 Croatia</option>
          <option value="+53">+53 Cuba</option>
          <option value="+599">+599 Curacao</option>
          <option value="+357">+357 Cyprus</option>
          <option value="+420">+420 Czech Republic</option>
          <option value="+243">+243 Democratic Republic of the Congo</option>
          <option value="+45">+45 Denmark</option>
          <option value="+253">+253 Djibouti</option>
          <option value="+1-767">+1-767 Dominica</option>
          <option value="+1-809">+1-809 Dominican Republic</option>
          <option value="+1-829">+1-829 Dominican Republic</option>
          <option value="+1-849">+1-849 Dominican Republic</option>
          <option value="+670">+670 East Timor</option>
          <option value="+593">+593 Ecuador</option>
          <option value="+20">+20 Egypt</option>
          <option value="+503">+503 El Salvador</option>
          <option value="+240">+240 Equatorial Guinea</option>
          <option value="+291">+291 Eritrea</option>
          <option value="+372">+372 Estonia</option>
          <option value="+251">+251 Ethiopia</option>
          <option value="+500">+500 Falkland Islands</option>
          <option value="+298">+298 Faroe Islands</option>
          <option value="+679">+679 Fiji</option>
          <option value="+358">+358 Finland</option>
          <option value="+33">+33 France</option>
          <option value="+689">+689 French Polynesia</option>
          <option value="+241">+241 Gabon</option>
          <option value="+220">+220 Gambia</option>
          <option value="+995">+995 Georgia</option>
          <option value="+49">+49 Germany</option>
          <option value="+233">+233 Ghana</option>
          <option value="+350">+350 Gibraltar</option>
          <option value="+30">+30 Greece</option>
          <option value="+299">+299 Greenland</option>
          <option value="+1-473">+1-473 Grenada</option>
          <option value="+1-671">+1-671 Guam</option>
          <option value="+502">+502 Guatemala</option>
          <option value="+44-1481">+44-1481 Guernsey</option>
          <option value="+224">+224 Guinea</option>
          <option value="+245">+245 Guinea-Bissau</option>
          <option value="+592">+592 Guyana</option>
          <option value="+509">+509 Haiti</option>
          <option value="+504">+504 Honduras</option>
          <option value="+852">+852 Hong Kong</option>
          <option value="+36">+36 Hungary</option>
          <option value="+354">+354 Iceland</option>
          <option value="+91">+91 India</option>
          <option value="+62">+62 Indonesia</option>
          <option value="+98">+98 Iran</option>
          <option value="+964">+964 Iraq</option>
          <option value="+353">+353 Ireland</option>
          <option value="+44-1624">+44-1624 Isle of Man</option>
          <option value="+972">+972 Israel</option>
          <option value="+39">+39 Italy</option>
          <option value="+225">+225 Ivory Coast</option>
          <option value="+1-876">+1-876 Jamaica</option>
          <option value="+81">+81 Japan</option>
          <option value="+44-1534">+44-1534 Jersey</option>
          <option value="+962">+962 Jordan</option>
          <option value="+7">+7 Kazakhstan</option>
          <option value="+254">+254 Kenya</option>
          <option value="+686">+686 Kiribati</option>
          <option value="+383">+383 Kosovo</option>
          <option value="+965">+965 Kuwait</option>
          <option value="+996">+996 Kyrgyzstan</option>
          <option value="+856">+856 Laos</option>
          <option value="+371">+371 Latvia</option>
          <option value="+961">+961 Lebanon</option>
          <option value="+266">+266 Lesotho</option>
          <option value="+231">+231 Liberia</option>
          <option value="+218">+218 Libya</option>
          <option value="+423">+423 Liechtenstein</option>
          <option value="+370">+370 Lithuania</option>
          <option value="+352">+352 Luxembourg</option>
          <option value="+853">+853 Macau</option>
          <option value="+389">+389 Macedonia</option>
          <option value="+261">+261 Madagascar</option>
          <option value="+265">+265 Malawi</option>
          <option value="+60">+60 Malaysia</option>
          <option value="+960">+960 Maldives</option>
          <option value="+223">+223 Mali</option>
          <option value="+356">+356 Malta</option>
          <option value="+692">+692 Marshall Islands</option>
          <option value="+222">+222 Mauritania</option>
          <option value="+230">+230 Mauritius</option>
          <option value="+262">+262 Mayotte</option>
          <option value="+52">+52 Mexico</option>
          <option value="+691">+691 Micronesia</option>
          <option value="+373">+373 Moldova</option>
          <option value="+377">+377 Monaco</option>
          <option value="+976">+976 Mongolia</option>
          <option value="+382">+382 Montenegro</option>
          <option value="+1-664">+1-664 Montserrat</option>
          <option value="+212">+212 Morocco</option>
          <option value="+258">+258 Mozambique</option>
          <option value="+95">+95 Myanmar</option>
          <option value="+264">+264 Namibia</option>
          <option value="+674">+674 Nauru</option>
          <option value="+977">+977 Nepal</option>
          <option value="+31">+31 Netherlands</option>
          <option value="+599">+599 Netherlands Antilles</option>
          <option value="+687">+687 New Caledonia</option>
          <option value="+64">+64 New Zealand</option>
          <option value="+505">+505 Nicaragua</option>
          <option value="+227">+227 Niger</option>
          <option value="+234">+234 Nigeria</option>
          <option value="+683">+683 Niue</option>
          <option value="+850">+850 North Korea</option>
          <option value="+1-670">+1-670 Northern Mariana Islands</option>
          <option value="+47">+47 Norway</option>
          <option value="+968">+968 Oman</option>
          <option value="+92">+92 Pakistan</option>
          <option value="+680">+680 Palau</option>
          <option value="+970">+970 Palestine</option>
          <option value="+507">+507 Panama</option>
          <option value="+675">+675 Papua New Guinea</option>
          <option value="+595">+595 Paraguay</option>
          <option value="+51">+51 Peru</option>
          <option value="+63">+63 Philippines</option>
          <option value="+64">+64 Pitcairn</option>
          <option value="+48">+48 Poland</option>
          <option value="+351">+351 Portugal</option>
          <option value="+1-787">+1-787 Puerto Rico</option>
          <option value="+1-939">+1-939 Puerto Rico</option>
          <option value="+974">+974 Qatar</option>
          <option value="+242">+242 Republic of the Congo</option>
          <option value="+262">+262 Reunion</option>
          <option value="+40">+40 Romania</option>
          <option value="+7">+7 Russia</option>
          <option value="+250">+250 Rwanda</option>
          <option value="+590">+590 Saint Barthelemy</option>
          <option value="+290">+290 Saint Helena</option>
          <option value="+1-869">+1-869 Saint Kitts and Nevis</option>
          <option value="+1-758">+1-758 Saint Lucia</option>
          <option value="+590">+590 Saint Martin</option>
          <option value="+508">+508 Saint Pierre and Miquelon</option>
          <option value="+1-784">+1-784 Saint Vincent and the Grenadines</option>
          <option value="+685">+685 Samoa</option>
          <option value="+378">+378 San Marino</option>
          <option value="+239">+239 Sao Tome and Principe</option>
          <option value="+966">+966 Saudi Arabia</option>
          <option value="+221">+221 Senegal</option>
          <option value="+381">+381 Serbia</option>
          <option value="+248">+248 Seychelles</option>
          <option value="+232">+232 Sierra Leone</option>
          <option value="+65">+65 Singapore</option>
          <option value="+1-721">+1-721 Sint Maarten</option>
          <option value="+421">+421 Slovakia</option>
          <option value="+386">+386 Slovenia</option>
          <option value="+677">+677 Solomon Islands</option>
          <option value="+252">+252 Somalia</option>
          <option value="+27">+27 South Africa</option>
          <option value="+82">+82 South Korea</option>
          <option value="+211">+211 South Sudan</option>
          <option value="+34">+34 Spain</option>
          <option value="+94">+94 Sri Lanka</option>
          <option value="+249">+249 Sudan</option>
          <option value="+597">+597 Suriname</option>
          <option value="+47">+47 Svalbard and Jan Mayen</option>
          <option value="+268">+268 Swaziland</option>
          <option value="+46">+46 Sweden</option>
          <option value="+41">+41 Switzerland</option>
          <option value="+963">+963 Syria</option>
          <option value="+886">+886 Taiwan</option>
          <option value="+992">+992 Tajikistan</option>
          <option value="+255">+255 Tanzania</option>
          <option value="+66">+66 Thailand</option>
          <option value="+228">+228 Togo</option>
          <option value="+690">+690 Tokelau</option>
          <option value="+676">+676 Tonga</option>
          <option value="+1-868">+1-868 Trinidad and Tobago</option>
          <option value="+216">+216 Tunisia</option>
          <option value="+90">+90 Turkey</option>
          <option value="+993">+993 Turkmenistan</option>
          <option value="+1-649">+1-649 Turks and Caicos Islands</option>
          <option value="+688">+688 Tuvalu</option>
          <option value="+1-340">+1-340 U.S. Virgin Islands</option>
          <option value="+256">+256 Uganda</option>
          <option value="+380">+380 Ukraine</option>
          <option value="+971">+971 UAE</option>
          <option value="+44">+44 UK</option>
          <option value="+1">+1 USA</option>
          <option value="+598">+598 Uruguay</option>
          <option value="+998">+998 Uzbekistan</option>
          <option value="+678">+678 Vanuatu</option>
          <option value="+379">+379 Vatican</option>
          <option value="+58">+58 Venezuela</option>
          <option value="+84">+84 Vietnam</option>
          <option value="+681">+681 Wallis and Futuna</option>
          <option value="+212">+212 Western Sahara</option>
          <option value="+967">+967 Yemen</option>
          <option value="+260">+260 Zambia</option>
          <option value="+263">+263 Zimbabwe</option>
        </select>
        <input name="mobile_number_bottom_unique" class="form-control w-100" placeholder="Phone">
        <input type="hidden" name="mobile" id="mobile_combined_bottom_unique_final">
      </div>
      <script>
        document.addEventListener('DOMContentLoaded', function () {
          const codeEl = document.querySelector('[name="country_code_bottom_unique"]');
          const numberEl = document.querySelector('[name="mobile_number_bottom_unique"]');
          const hiddenEl = document.getElementById('mobile_combined_bottom_unique_final');
          function updateCombined() {
            hiddenEl.value = codeEl.value + numberEl.value;
          }
          codeEl.addEventListener('change', updateCombined);
          numberEl.addEventListener('input', updateCombined);
          updateCombined();
        });
      </script>
      <?php endif; ?>

      <div class="form-group mb-2">
        <input class="form-control" name="email" value="<?php echo esc_attr($user_email); ?>" type="email" placeholder="<?php echo houzez_option('spl_con_email', 'Email'); ?>">
      </div>

      <?php if ($hide_form_fields['message'] != 1): ?>
      <div class="form-group form-group-textarea mb-2">
        <textarea class="form-control hz-form-message" name="message" rows="4" placeholder="<?php echo houzez_option('spl_con_message', 'Message'); ?>"><?php echo houzez_option('spl_con_interested', "Hello, I am interested in"); ?> [<?php echo get_the_title(); ?>]</textarea>
      </div>
      <?php endif; ?>

      <?php if ($hide_form_fields['usertype'] != 1): ?>
      <div class="form-group mb-2">
        <select name="user_type" class="form-control">
          <option value="buyer">I'm a buyer</option>
          <option value="agent">I'm an agent</option>
          <option value="other">Other</option>
        </select>
      </div>
      <?php endif; ?>

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

      <?php get_template_part('template-parts/google', 'reCaptcha'); ?>
      <div class="form_messages"></div>

      <div class="property-schedule-tour-type-form d-flex justify-content-between gap-2 mb-2">
        <button type="button" class="houzez-ele-button houzez_agent_property_form btn btn-secondary w-100">
          <?php get_template_part('template-parts/loader'); ?>
          <span><?php echo houzez_option('spl_btn_send', 'Send Email'); ?></span>
        </button>
        <?php if ($agent_number && $agent_mobile_num && !wp_is_mobile()): ?>
        <a href="tel:<?php echo esc_attr($agent_mobile_call); ?>" class="btn btn-call hz-btn-call w-100">
          <span class="hide-on-click"><?php echo houzez_option('spl_btn_call', 'Call'); ?></span>
          <span class="show-on-click"><?php echo esc_attr($agent_number); ?></span>
        </a>
        <?php endif; ?>
      </div>
      <?php if ($agent_whatsapp_call && $agent_whatsapp_num): ?>
      <a target="_blank" href="https://api.whatsapp.com/send?phone=<?php echo esc_attr($agent_whatsapp_call); ?>&text=<?php echo houzez_option('spl_con_interested', "Hello, I am interested in").' ['.get_the_title().'] '.get_permalink(); ?>" class="btn btn-secondary-outlined w-100 hz-btn-whatsapp mb-2"><i class="houzez-icon icon-messaging-whatsapp me-1"></i> <?php esc_html_e('WhatsApp', 'houzez'); ?></a>
      <?php endif; ?>
    </form>
  </div>
</div>
<?php endif; ?>
