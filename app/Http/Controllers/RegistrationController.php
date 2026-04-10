<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Mail\RegistrationSuccess;

class RegistrationController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->all();

        // Transform boolean fields from 'yes'/'no' to true/false
        $booleanFields = ['email_orders', 'phone_orders', 'telephone_orders', 'whatsapp_orders', 'agree_terms'];
        foreach ($booleanFields as $field) {
            if (isset($validatedData[$field])) {
                $validatedData[$field] = $validatedData[$field] === 'yes' ? true : false;
            }
        }

        $validator = Validator::make($validatedData, [
            'email' => 'required|email|unique:registrations',
            //'password' => 'required|confirmed',  //16.01.2026
            'partner_type' => 'required|in:supplier,client',  //16.01.2026
            'company_name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'type_of_business' => 'required|string|max:255',
            'legal_entity' => 'required|string|max:255',
            'ceo_name' => 'required|string|max:255',
            'invoice_address' => 'required|string|max:255',
            // 'accountant_name' => 'required|string|max:255',
            // 'accountant_email' => 'required|email|max:255',
            'company_reg_no' => 'required|string|max:255',
            'vat_reg_no' => 'required|string|max:255',
            'num_employees' => 'required|integer',
            'date_registration' => 'required|date',
            'bank_name' => 'required|string|max:255',
            'iban' => 'required|string|max:255',
            'bank_address' => 'required|string|max:255',
            'swift' => 'required|string|max:255',
            'country_of_bank' => 'required|string|max:255',
            'account_holder' => 'required|string|max:255',
            'bank_phone' => 'required|string|max:255',
            'years_with_bank' => 'required|integer',
            'email_orders' => 'required|boolean',
            'phone_orders' => 'required|boolean',
            'telephone_orders' => 'required|boolean',
            'whatsapp_orders' => 'required|boolean',
            'trader1_name' => 'required|string|max:255',
            'trader1_phone' => 'required|string|max:255',
            'trader1_email' => 'required|email|max:255',
            'company_incorporation_cert' => 'required|file|mimes:pdf,jpg,jpeg,png',
            'vat_cert' => 'required|file|mimes:pdf,jpg,jpeg,png',
            'completed_refs' => 'required|file|mimes:pdf,jpg,jpeg,png',
            'bank_details_cert' => 'required|file|mimes:pdf,jpg,jpeg,png',
            'utility_bill_copy' => 'required|file|mimes:pdf,jpg,jpeg,png',
            'director_id_doc' => 'required|file|mimes:pdf,jpg,jpeg,png',
            'agree_terms' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            // return response()->json(['errors' => $validator->errors()], 422);
            return redirect()->route('register')->with('errors', $validator->errors());
        }

        $data = $validatedData;
        // $data['password'] = Hash::make($request->password); //16.01.2026
        $data['password'] = Hash::make('12345678');
        // $data['accountant_name'] = "demo"
        // $data['accountant_email'] = "ac@gmail.com";

        $fileFields = [
            'company_incorporation_cert',
            'vat_cert',
            'completed_refs',
            'bank_details_cert',
            'utility_bill_copy',
            'director_id_doc'
        ];

        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                $data[$field] = $request->file($field)->store('documents', 'public');
            }
        }

        $registration = Registration::create($data);

        // Generate PDF of the registration data
        $pdf = Pdf::loadView('pdfs.registration', ['registration' => $registration]);
        
        // Render the PDF first
        $pdf->render();
        
        // Add header and footer to every page using page_script
        $pdf->getDomPDF()->getCanvas()->page_script('
            $font = $fontMetrics->getFont("helvetica", "normal");
            $fontBold = $fontMetrics->getFont("helvetica", "bold");
            
            $pageWidth = $pdf->get_width();
            $pageHeight = $pdf->get_height();
            
            // Draw Header
            $pdf->rectangle(0, 0, $pageWidth, 120, array(0.77, 0.15, 0.15), 2, array(0.77, 0.15, 0.15));
            $logoPath = "' . public_path('images/logo.png') . '";
            if (file_exists($logoPath)) {
                $pdf->image($logoPath, ($pageWidth - 180) / 2, 15, 180, 30);
            }
            $pdf->line(40, 60, $pageWidth - 40, 60, array(0.08, 0.39, 0.75), 3);
            $titleText = "Registration Details";
            $textWidth = $fontMetrics->getTextWidth($titleText, $fontBold, 16);
            $pdf->text(($pageWidth - $textWidth) / 2, 80, $titleText, $fontBold, 16, array(0.77, 0.15, 0.15));
            
            // Draw Footer
            $pdf->rectangle(0, $pageHeight - 60, $pageWidth, 60, array(0.96, 0.96, 0.96), 2, array(0.96, 0.96, 0.96));
            $pdf->line(0, $pageHeight - 60, $pageWidth, $pageHeight - 60, array(0.77, 0.15, 0.15), 3);
            
            $footerText = "Generated by Smartify Tech | ' . date('F d, Y') . '";
            $textWidth = $fontMetrics->getTextWidth($footerText, $font, 9);
            $pdf->text(($pageWidth - $textWidth) / 2, $pageHeight - 35, $footerText, $font, 9, array(0.4, 0.4, 0.4));
            
            $pageText = "Page " . $PAGE_NUM . " of " . $PAGE_COUNT;
            $textWidth = $fontMetrics->getTextWidth($pageText, $font, 8);
            $pdf->text(($pageWidth - $textWidth) / 2, $pageHeight - 20, $pageText, $font, 8, array(0.6, 0.6, 0.6));
        ');

        // Define the admin email address
        $adminEmail = 'info@smartify-tech.com'; // Replace with your admin email

        // Send the email to the user and CC the admin
        Mail::to($registration->email)
            ->cc($adminEmail)
            ->send(new RegistrationSuccess($registration, $pdf, $fileFields));

        return redirect('/')->with('success', "Registration successful! A confirmation email has been sent to {$registration->email}.");
    }

    public function validateEmail(Request $request)
    {
        $email = $request->input('email');
        
        $validator = Validator::make(['email' => $email], [
            'email' => 'required|email|unique:registrations'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'valid' => false,
                'message' => 'This email is already registered'
            ]);
        }

        return response()->json([
            'valid' => true,
            'message' => 'Email is available'
        ]);
    }

    public function validateCompanyRegNo(Request $request)
    {
        $companyRegNo = $request->input('company_reg_no');
        
        $validator = Validator::make(['company_reg_no' => $companyRegNo], [
            'company_reg_no' => 'required|string|unique:registrations'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'valid' => false,
                'message' => 'This company registration number is already registered'
            ]);
        }

        return response()->json([
            'valid' => true,
            'message' => 'Company registration number is available'
        ]);
    }

    public function previewPdf($id)
    {
        $registration = Registration::findOrFail($id);
        
        // Generate PDF of the registration data
        $pdf = Pdf::loadView('pdfs.registration', ['registration' => $registration]);
        
        // Render the PDF first
        $pdf->render();
        
        // Add header and footer to every page using page_script
        $pdf->getDomPDF()->getCanvas()->page_script('
            $font = $fontMetrics->getFont("helvetica", "normal");
            $fontBold = $fontMetrics->getFont("helvetica", "bold");
            
            $pageWidth = $pdf->get_width();
            $pageHeight = $pdf->get_height();
            
            // Draw Header
            $pdf->rectangle(0, 0, $pageWidth, 120, array(0.77, 0.15, 0.15), 2, array(0.77, 0.15, 0.15));
            $logoPath = "' . public_path('images/logo.png') . '";
            if (file_exists($logoPath)) {
                $pdf->image($logoPath, ($pageWidth - 180) / 2, 15, 180, 30);
            }
            $pdf->line(40, 60, $pageWidth - 40, 60, array(0.08, 0.39, 0.75), 3);
            $titleText = "Registration Details";
            $textWidth = $fontMetrics->getTextWidth($titleText, $fontBold, 16);
            $pdf->text(($pageWidth - $textWidth) / 2, 80, $titleText, $fontBold, 16, array(0.77, 0.15, 0.15));
            
            // Draw Footer
            $pdf->rectangle(0, $pageHeight - 60, $pageWidth, 60, array(0.96, 0.96, 0.96), 2, array(0.96, 0.96, 0.96));
            $pdf->line(0, $pageHeight - 60, $pageWidth, $pageHeight - 60, array(0.77, 0.15, 0.15), 3);
            
            $footerText = "Generated by Smartify Tech | ' . date('F d, Y') . '";
            $textWidth = $fontMetrics->getTextWidth($footerText, $font, 9);
            $pdf->text(($pageWidth - $textWidth) / 2, $pageHeight - 35, $footerText, $font, 9, array(0.4, 0.4, 0.4));
            
            $pageText = "Page " . $PAGE_NUM . " of " . $PAGE_COUNT;
            $textWidth = $fontMetrics->getTextWidth($pageText, $font, 8);
            $pdf->text(($pageWidth - $textWidth) / 2, $pageHeight - 20, $pageText, $font, 8, array(0.6, 0.6, 0.6));
        ');
        
        // Return PDF for preview in browser
        return $pdf->stream('registration_preview.pdf');
    }
}
