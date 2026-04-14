@extends('layouts.app')

@section('title', 'Register - Smartify Tech')

@section('content')
<div class="register-form-wrapper">
    <div class="register-info-container">
        <div class="register-info-header">
            <h2 class="register-info-title">{{ __('messages.register_title') }}</h2>
            <p class="register-info-subtitle">{{ __('messages.register_subtitle') }}</p>
        </div>
        
        <!-- Step Progress -->
        <div class="step-progress">
            <div class="step-item active" data-step="1">
                <div class="step-number">1</div>
                <div class="step-title">{{ __('messages.step1_title') }}</div>
            </div>
            <div class="step-item" data-step="2">
                <div class="step-number">2</div>
                <div class="step-title">{{ __('messages.step2_title') }}</div>
            </div>
            <div class="step-item" data-step="3">
                <div class="step-number">3</div>
                <div class="step-title">{{ __('messages.step3_title') }}</div>
            </div>
            <div class="step-item" data-step="4">
                <div class="step-number">4</div>
                <div class="step-title">{{ __('messages.step4_title') }}</div>
            </div>
            <div class="step-item" data-step="5">
                <div class="step-number">5</div>
                <div class="step-title">{{ __('messages.step5_title') }}</div>
            </div>
            <div class="step-item" data-step="6">
                <div class="step-number">6</div>
                <div class="step-title">{{ __('messages.step6_title') }}</div>
            </div>
            <div class="step-item" data-step="7">
                <div class="step-number">7</div>
                <div class="step-title">{{ __('messages.step7_title') }}</div>
            </div>
        </div>
        
        <form class="modern-register-form" method="post" action="{{ route('register.submit') }}" enctype="multipart/form-data">
            @csrf
                    
                    <!-- Step 1: General Information -->
                    <div class="form-step active" data-step="1">
                        <div class="step-header">
                            <h3>{{ __('messages.step1_header') }}</h3>
                            <p>{{ __('messages.step1_subheader') }}</p>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="email" class="form-label">{{ __('messages.email_address_star') }}</label>
                                <input type="email" id="email" name="email" class="form-input" placeholder="{{ __('messages.your_email') }}" required>
                            </div>
                            {{--<div class="form-group">
                                <label for="password" class="form-label">{{ __('messages.password_star') }}</label>
                                <input type="password" id="password" name="password" class="form-input" placeholder="{{ __('messages.password') }}" required>
                            </div>
                            <div class="form-group">
                                <label for="password_confirmation" class="form-label">{{ __('messages.confirm_password_star') }}</label>
                                <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" placeholder="{{ __('messages.confirm_password') }}" required>
                            </div>--}}
                            <div class="form-group">
                                <label for="partner_type" class="form-label">
                                    {{ __('messages.partner_type_star') }}
                                </label>

                                <select id="partner_type" name="partner_type" class="form-input" required>
                                    <option value="">{{ __('messages.partner_type') }}</option>

                                    <option value="supplier"
                                        {{ request('partner_type') == 'supplier' ? 'selected' : '' }}>
                                        {{ __('messages.supplier') }}
                                    </option>

                                    <option value="client"
                                        {{ request('partner_type') == 'client' ? 'selected' : '' }}>
                                        {{ __('messages.client') }}
                                    </option>
                                </select>
                            </div>


                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="company_name" class="form-label">{{ __('messages.company_name_star') }}</label>
                                <input type="text" id="company_name" name="company_name" class="form-input" placeholder="{{ __('messages.company_name') }}" required>
                            </div>
                            <div class="form-group">
                                <label for="phone" class="form-label">{{ __('messages.phone_number_star') }}</label>
                                <input type="tel" id="phone" name="phone" class="form-input" placeholder="{{ __('messages.phone_number_placeholder') }}" required>
                            </div>
                            <div class="form-group">
                                <label for="type_of_business" class="form-label">{{ __('messages.type_of_business_star') }}</label>
                                <input type="text" id="type_of_business" name="type_of_business" class="form-input" placeholder="{{ __('messages.business_type') }}" required>
                            </div>
                            <div class="form-group">
                                <label for="legal_entity" class="form-label">{{ __('messages.type_of_legal_entity_star') }}</label>
                                <input type="text" id="legal_entity" name="legal_entity" class="form-input" placeholder="{{ __('messages.legal_entity_type') }}" required>
                            </div>
                            <div class="form-group">
                                <label for="ceo_name" class="form-label">{{ __('messages.ceo_name_star') }}</label>
                                <input type="text" id="ceo_name" name="ceo_name" class="form-input" placeholder="{{ __('messages.ceo_name') }}" required>
                            </div>
                            <div class="form-group">
                                <label for="website" class="form-label">{{ __('messages.website') }}</label>
                                <input type="text" id="website" name="website" class="form-input" placeholder="{{ __('messages.website') }}">
                            </div>
                            <div class="form-group">
                                <label for="invoice_address" class="form-label">{{ __('messages.invoice_delivery_address_star') }}</label>
                                <input type="text" id="invoice_address" name="invoice_address" class="form-input" placeholder="{{ __('messages.invoice_address') }}" required>
                            </div>
                            <div class="form-group">
                                <label for="delivery_address" class="form-label">{{ __('messages.delivery_address') }}</label>
                                <input type="text" id="delivery_address" name="delivery_address" class="form-input" placeholder="{{ __('messages.delivery_address') }}">
                            </div>
                            <!-- <div class="form-group">
                                <label for="accountant_name" class="form-label">{{ __('messages.accountant_name_star') }}</label>
                                <input type="text" id="accountant_name" name="accountant_name" class="form-input" placeholder="{{ __('messages.accountant_name') }}" required>
                            </div>
                            <div class="form-group">
                                <label for="accountant_email" class="form-label">{{ __('messages.accountant_email_star') }}</label>
                                <input type="email" id="accountant_email" name="accountant_email" class="form-input" placeholder="{{ __('messages.accountant_email') }}" required>
                            </div> -->
                            <div class="form-group">
                                <label for="company_reg_no" class="form-label">{{ __('messages.company_reg_no_star') }}</label>
                                <input type="text" id="company_reg_no" name="company_reg_no" class="form-input" placeholder="{{ __('messages.reg_no') }}" required>
                            </div>
                            <div class="form-group">
                                <label for="vat_reg_no" class="form-label">{{ __('messages.vat_reg_no_star') }}</label>
                                <input type="text" id="vat_reg_no" name="vat_reg_no" class="form-input" placeholder="{{ __('messages.vat_no') }}" required>
                            </div>
                            <div class="form-group">
                                <label for="num_employees" class="form-label">{{ __('messages.num_employees_star') }}</label>
                                <input type="number" id="num_employees" name="num_employees" class="form-input" placeholder="{{ __('messages.num_employees') }}" required>
                            </div>
                            <div class="form-group">
                                <label for="date_registration" class="form-label">{{ __('messages.date_registration_star') }}</label>
                                <input type="date" id="date_registration" name="date_registration" class="form-input" placeholder="{{ __('messages.date_registration') }}" required>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Step 2: Bank Details -->
                    <div class="form-step" data-step="2">
                        <div class="step-header">
                            <h3>{{ __('messages.step2_header') }}</h3>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="bank_name" class="form-label">{{ __('messages.bank_name_star') }}</label>
                                <input type="text" id="bank_name" name="bank_name" class="form-input" placeholder="{{ __('messages.bank_name') }}" required>
                            </div>
                            <div class="form-group">
                                <label for="iban" class="form-label">{{ __('messages.iban_star') }}</label>
                                <input type="text" id="iban" name="iban" class="form-input" placeholder="{{ __('messages.iban') }}" required>
                            </div>
                            <div class="form-group">
                                <label for="bank_address" class="form-label">{{ __('messages.address_star') }}</label>
                                <input type="text" id="bank_address" name="bank_address" class="form-input" placeholder="{{ __('messages.bank_address') }}" required>
                            </div>
                            <div class="form-group">
                                <label for="swift" class="form-label">{{ __('messages.swift_star') }}</label>
                                <input type="text" id="swift" name="swift" class="form-input" placeholder="{{ __('messages.swift') }}" required>
                            </div>
                            <div class="form-group">
                                <label for="country_of_bank" class="form-label">{{ __('messages.country_of_bank_star') }}</label>
                                <input type="text" id="country_of_bank" name="country_of_bank" class="form-input" placeholder="{{ __('messages.country') }}" required>
                            </div>
                            <div class="form-group">
                                <label for="account_holder" class="form-label">{{ __('messages.account_holder_star') }}</label>
                                <input type="text" id="account_holder" name="account_holder" class="form-input" placeholder="{{ __('messages.name') }}" required>
                            </div>
                            <div class="form-group">
                                <label for="bank_phone" class="form-label">{{ __('messages.phone_number_star') }}</label>
                                <input type="tel" id="bank_phone" name="bank_phone" class="form-input" placeholder="06" required>
                            </div>
                            <!-- <div class="form-group">
                                <label for="years_with_bank" class="form-label">{{ __('messages.years_with_bank_star') }}</label>
                                <input type="number" id="years_with_bank" name="years_with_bank_star" class="form-input" placeholder="{{ __('messages.years') }}" required>
                            </div> -->
                        </div>
                    </div>
                    
                    <!-- Step 3: References -->
                    <div class="form-step" data-step="3">
                        <div class="step-header">
                            <h3>{{ __('messages.step3_header') }}</h3>
                            <p>{{ __('messages.step3_subheader') }}</p>
                        </div>
                        <div class="person-section">
                            <h4>{{ __('messages.trade_ref1') }}</h4>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="ref1_bank_name" class="form-label">{{ __('messages.bank_name_optional') }}</label>
                                    <input type="text" id="ref1_bank_name" name="ref1_bank_name" class="form-input" placeholder="{{ __('messages.bank_name') }}">
                                </div>
                                <div class="form-group">
                                    <label for="ref1_address" class="form-label">{{ __('messages.address') }}</label>
                                    <input type="text" id="ref1_address" name="ref1_address" class="form-input" placeholder="{{ __('messages.bank_address') }}">
                                </div>
                                <div class="form-group">
                                    <label for="ref1_phone" class="form-label">{{ __('messages.phone_number') }}</label>
                                    <input type="tel" id="ref1_phone" name="ref1_phone" class="form-input" placeholder="06">
                                </div>
                                <div class="form-group">
                                    <label for="ref1_name" class="form-label">{{ __('messages.name_surname') }}</label>
                                    <input type="text" id="ref1_name" name="ref1_name" class="form-input" placeholder="{{ __('messages.name') }}">
                                </div>
                            </div>
                        </div>
                        <div class="person-section">
                            <h4>{{ __('messages.trade_ref2') }}</h4>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="ref2_bank_name" class="form-label">{{ __('messages.bank_name_optional') }}</label>
                                    <input type="text" id="ref2_bank_name" name="ref2_bank_name" class="form-input" placeholder="{{ __('messages.bank_name') }}">
                                </div>
                                <div class="form-group">
                                    <label for="ref2_address" class="form-label">{{ __('messages.address') }}</label>
                                    <input type="text" id="ref2_address" name="ref2_address" class="form-input" placeholder="{{ __('messages.bank_address') }}">
                                </div>
                                <div class="form-group">
                                    <label for="ref2_phone" class="form-label">{{ __('messages.phone_number') }}</label>
                                    <input type="tel" id="ref2_phone" name="ref2_phone" class="form-input" placeholder="06">
                                </div>
                                <div class="form-group">
                                    <label for="ref2_name" class="form-label">{{ __('messages.name_surname') }}</label>
                                    <input type="text" id="ref2_name" name="ref2_name" class="form-input" placeholder="{{ __('messages.name') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Step 4: Authorization for online and phone orders -->
                    <div class="form-step" data-step="4">
                        <div class="step-header">
                            <h3>{{ __('messages.step4_header') }}</h3>
                        </div>
                        <div class="form-group">
                            <label class="form-label">{{ __('messages.email_orders_allowed') }}</label>
                            <div class="form-radio">
                                <label><input type="radio" name="email_orders" value="no" required> {{ __('messages.no') }}</label>
                                <label><input type="radio" name="email_orders" value="yes" required> {{ __('messages.yes') }}</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">{{ __('messages.phone_orders_allowed') }}</label>
                            <div class="form-radio">
                                <label><input type="radio" name="phone_orders" value="no" required> {{ __('messages.no') }}</label>
                                <label><input type="radio" name="phone_orders" value="yes" required> {{ __('messages.yes') }}</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">{{ __('messages.telephone_orders_allowed') }}</label>
                            <div class="form-radio">
                                <label><input type="radio" name="telephone_orders" value="no" required> {{ __('messages.no') }}</label>
                                <label><input type="radio" name="telephone_orders" value="yes" required> {{ __('messages.yes') }}</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">{{ __('messages.whatsapp_orders_allowed') }}</label>
                            <div class="form-radio">
                                <label><input type="radio" name="whatsapp_orders" value="no" required> {{ __('messages.no') }}</label>
                                <label><input type="radio" name="whatsapp_orders" value="yes" required> {{ __('messages.yes') }}</label>
                            </div>
                        </div>
                    </div>

                    <!-- Step 5: Traders / Contact for sale & purchase / Brokers -->
                    <div class="form-step" data-step="5">
                        <div class="step-header">
                            <h3>{{ __('messages.step5_header') }}</h3>
                        </div>
                        <div class="person-section">
                            <h4>{{ __('messages.person1') }}</h4>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="trader1_name" class="form-label">{{ __('messages.first_last_name_star') }}</label>
                                    <input type="text" id="trader1_name" name="trader1_name" class="form-input" placeholder="{{ __('messages.name') }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="trader1_phone" class="form-label">{{ __('messages.phone_number_star') }}</label>
                                    <input type="tel" id="trader1_phone" name="trader1_phone" class="form-input" placeholder="{{ __('messages.phone') }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="trader1_email" class="form-label">{{ __('messages.email_address_star') }}</label>
                                    <input type="email" id="trader1_email" name="trader1_email" class="form-input" placeholder="{{ __('messages.email') }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="person-section">
                            <h4>{{ __('messages.person2') }}</h4>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="trader2_name" class="form-label">{{ __('messages.first_last_name') }}</label>
                                    <input type="text" id="trader2_name" name="trader2_name" class="form-input" placeholder="{{ __('messages.name') }}">
                                </div>
                                <div class="form-group">
                                    <label for="trader2_skype" class="form-label">{{ __('messages.skype_id') }}</label>
                                    <input type="text" id="trader2_skype" name="trader2_skype" class="form-input" placeholder="{{ __('messages.skype') }}">
                                </div>
                                <div class="form-group">
                                    <label for="trader2_phone" class="form-label">{{ __('messages.phone_number') }}</label>
                                    <input type="tel" id="trader2_phone" name="trader2_phone" class="form-input" placeholder="{{ __('messages.phone') }}">
                                </div>
                                <div class="form-group">
                                    <label for="trader2_email" class="form-label">{{ __('messages.email_address') }}</label>
                                    <input type="email" id="trader2_email" name="trader2_email" class="form-input" placeholder="{{ __('messages.email') }}">
                                </div>
                            </div>
                        </div>
                        <div class="person-section">
                            <h4>{{ __('messages.person3') }}</h4>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="trader3_name" class="form-label">{{ __('messages.first_last_name') }}</label>
                                    <input type="text" id="trader3_name" name="trader3_name" class="form-input" placeholder="{{ __('messages.name') }}">
                                </div>
                                <div class="form-group">
                                    <label for="trader3_skype" class="form-label">{{ __('messages.skype_id') }}</label>
                                    <input type="text" id="trader3_skype" name="trader3_skype" class="form-input" placeholder="{{ __('messages.skype') }}">
                                </div>
                                <div class="form-group">
                                    <label for="trader3_phone" class="form-label">{{ __('messages.phone_number') }}</label>
                                    <input type="tel" id="trader3_phone" name="trader3_phone" class="form-input" placeholder="{{ __('messages.phone') }}">
                                </div>
                                <div class="form-group">
                                    <label for="trader3_email" class="form-label">{{ __('messages.email_address') }}</label>
                                    <input type="email" id="trader3_email" name="trader3_email" class="form-input" placeholder="{{ __('messages.email') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 6: IMPORTANT - PLEASE SUPPLY THE FOLLOWING: -->
                    <div class="form-step" data-step="6">
                        <div class="step-header">
                            <h3>{{ __('messages.step6_header') }}</h3>
                        </div>
                        <div class="form-group">
                            <label for="company_incorporation_cert" class="form-label">{{ __('messages.company_incorporation_cert_star') }}</label>
                            <input type="file" id="company_incorporation_cert" name="company_incorporation_cert" class="form-input" accept=".pdf,.jpg,.jpeg,.png" required>
                        </div>
                        <div class="form-group">
                            <label for="vat_cert" class="form-label">{{ __('messages.vat_cert_star') }}</label>
                            <input type="file" id="vat_cert" name="vat_cert" class="form-input" accept=".pdf,.jpg,.jpeg,.png" required>
                        </div>
                        <div class="form-group">
                            <label for="completed_refs" class="form-label">{{ __('messages.completed_refs_star') }}</label>
                            <input type="file" id="completed_refs" name="completed_refs" class="form-input" accept=".pdf,.jpg,.jpeg,.png" required>
                        </div>
                        <div class="form-group">
                            <label for="bank_details_cert" class="form-label">{{ __('messages.bank_details_cert_star') }}</label>
                            <input type="file" id="bank_details_cert" name="bank_details_cert" class="form-input" accept=".pdf,.jpg,.jpeg,.png" required>
                        </div>
                        <div class="form-group">
                            <label for="utility_bill_copy" class="form-label">{{ __('messages.utility_bill_copy_star') }}</label>
                            <input type="file" id="utility_bill_copy" name="utility_bill_copy" class="form-input" accept=".pdf,.jpg,.jpeg,.png" required>
                        </div>
                        <div class="form-group">
                            <label for="director_id_doc" class="form-label">{{ __('messages.director_id_doc_star') }}</label>
                            <input type="file" id="director_id_doc" name="director_id_doc" class="form-input" accept=".pdf,.jpg,.jpeg,.png" required>
                        </div>
                    </div>

                    <!-- Step 7: Terms & Conditions -->
                    <div class="form-step" data-step="7">
                        <div class="step-header">
                            <h3>{{ __('messages.step7_header') }}</h3>
                        </div>
                        <div class="terms-content">
                            <h4>{{ __('messages.agreement_terms') }}</h4>
                            <div class="terms-text">
                                <p>{{ __('messages.term1') }}</p>
                                <p>{{ __('messages.term2') }}</p>
                                <p>{{ __('messages.term3') }}</p>
                                <p>{{ __('messages.term4') }}</p>
                                <p>{{ __('messages.term5') }}</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">{{ __('messages.agree_terms_question') }}</label>
                            <div class="form-radio">
                                <label><input type="radio" name="agree_terms" value="no" required> {{ __('messages.dont_agree') }}</label>
                                <label><input type="radio" name="agree_terms" value="yes" required> {{ __('messages.agree') }}</label>
                            </div>
                        </div>
                    </div>

                    <!-- Step 8: Final Review -->
                    <div class="form-step" data-step="8">
                        <div class="step-header">
                            <h3>{{ __('messages.final_review') }}</h3>
                            <p>{{ __('messages.final_review_subtitle') }}</p>
                        </div>

                        <div class="progress-indicator">
                            <ul>
                                <li class="progress-step" data-step="1">✓</li>
                                <li class="progress-step" data-step="2">✓</li>
                                <li class="progress-step" data-step="3">✓</li>
                                <li class="progress-step" data-step="4">✓</li>
                                <li class="progress-step" data-step="5">✓</li>
                                <li class="progress-step" data-step="6">✓</li>
                                <li class="progress-step" data-step="7">✓</li>
                                <li class="progress-step" data-step="8">✓</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="form-navigation">
                        <button type="button" class="btn-prev" id="prevBtn" style="display: none;">{{ __('messages.previous') }}</button>
                        <button type="button" class="btn-next" id="nextBtn">{{ __('messages.next') }}</button>
                        <button type="submit" class="btn-submit" id="submitBtn" style="display: none;">{{ __('messages.submit_application') }}</button>
                    </div>
                </form>
    </div>
</div>
@endsection