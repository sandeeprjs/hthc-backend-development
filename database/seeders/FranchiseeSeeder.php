<?php

namespace Database\Seeders;

use App\Franchisee;
use Illuminate\Database\Seeder;

class FranchiseeSeeder extends Seeder
{
    public function run()
    {
        $franchisees = [
            [
                'branch_id' => 1,
                'code' => 'FR001',
                'mobile_number' => '9876543210',
                'email' => 'koramangala@example.com',
                'enterprise_name' => 'Koramangala Hub',
                'add_line_1' => '123 Koramangala Main Road',
                'city' => 'Bangalore',
                'state' => 'Karnataka',
                'pincode_id' => 1,
                'contact_person_name' => 'Arun Kumar',
                'phone_number' => '08012345678',
                'franchisee_type' => 'Hub',
                'current_bank_name' => 'State Bank of India',
                'branch_name' => 'Koramangala',
                'account_number' => '1234567890',
                'ifsc_code' => 'SBIN0001234',
                'avatar' => 'koramangala_avatar.png',
                'doc_proof' => 'koramangala_proof.pdf',
            ],
            [
                'branch_id' => 2,
                'code' => 'FR002',
                'mobile_number' => '9876543211',
                'email' => 'whitefield@example.com',
                'enterprise_name' => 'Whitefield Branch',
                'add_line_1' => '456 Whitefield Main Road',
                'city' => 'Bangalore',
                'state' => 'Karnataka',
                'pincode_id' => 2,
                'contact_person_name' => 'Sneha Sharma',
                'phone_number' => '08012345679',
                'franchisee_type' => 'Branch',
                'current_bank_name' => 'ICICI Bank',
                'branch_name' => 'Whitefield',
                'account_number' => '0987654321',
                'ifsc_code' => 'ICIC0000987',
                'avatar' => 'whitefield_avatar.png',
                'doc_proof' => 'whitefield_proof.pdf',
            ],
            [
                'branch_id' => 3,
                'code' => 'FR003',
                'mobile_number' => '9876543212',
                'email' => 'jayanagar@example.com',
                'enterprise_name' => 'Jayanagar Branch',
                'add_line_1' => '789 Jayanagar Main Road',
                'city' => 'Bangalore',
                'state' => 'Karnataka',
                'pincode_id' => 3,
                'contact_person_name' => 'Rajesh Kumar',
                'phone_number' => '08012345680',
                'franchisee_type' => 'Branch',
                'current_bank_name' => 'Axis Bank',
                'branch_name' => 'Jayanagar',
                'account_number' => '1122334455',
                'ifsc_code' => 'UTIB0001122',
                'avatar' => 'jayanagar_avatar.png',
                'doc_proof' => 'jayanagar_proof.pdf',
            ],
            [
                'branch_id' => 4,
                'code' => 'FR004',
                'mobile_number' => '9876543213',
                'email' => 'indiranagar@example.com',
                'enterprise_name' => 'Indiranagar Hub',
                'add_line_1' => '101 Indiranagar Main Road',
                'city' => 'Bangalore',
                'state' => 'Karnataka',
                'pincode_id' => 4,
                'contact_person_name' => 'Meera Rao',
                'phone_number' => '08012345681',
                'franchisee_type' => 'Hub',
                'current_bank_name' => 'HDFC Bank',
                'branch_name' => 'Indiranagar',
                'account_number' => '6677889900',
                'ifsc_code' => 'HDFC0001234',
                'avatar' => 'indiranagar_avatar.png',
                'doc_proof' => 'indiranagar_proof.pdf',
            ],
        ];

        foreach ($franchisees as $data) {
            Franchisee::updateOrCreate(
                ['code' => $data['code']],
                $data
            );
        }
    }
}
