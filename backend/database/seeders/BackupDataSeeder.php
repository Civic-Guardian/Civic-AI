<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BackupDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $data = array (
  'ai_analyses' => 
  array (
    0 => 
    array (
      'id' => 1,
      'hazard_id' => NULL,
      'generated_summary' => 'AI Analysis unavailable. A potential hazard was reported at this location.',
      'petition_draft' => 'To,
The Municipal Commissioner,

Subject: Civic Hazard Report

Please investigate the civic hazard reported at this location.

Sincerely,
A Concerned Citizen',
      'predicted_severity' => 'Medium',
      'severity_reasoning' => NULL,
      'is_duplicate' => 0,
      'duplicate_of_id' => NULL,
      'raw_payload' => '{"predicted_category":"Unknown Hazard","predicted_severity":"Medium","confidence_score":75,"generated_summary":"AI Analysis unavailable. A potential hazard was reported at this location.","petition_draft":"To,\\nThe Municipal Commissioner,\\n\\nSubject: Civic Hazard Report\\n\\nPlease investigate the civic hazard reported at this location.\\n\\nSincerely,\\nA Concerned Citizen","status":"Failed","error":"Gemini API call failed or response malformed: {\\n  \\"candidates\\": [\\n    {\\n      \\"content\\": {\\n        \\"parts\\": [\\n          {\\n            \\"text\\": \\"{\\\\n  \\\\\\"predicted_category\\\\\\": \\\\\\"Sanitation Issue\\\\\\",\\\\n  \\\\\\"predicted_severity\\\\\\": \\\\\\"Moderate\\\\\\",\\\\n  \\\\\\"confidence_score\\\\\\": 0.8,\\\\n  \\\\\\"generated_summary\\\\\\": \\\\\\"A civic safety hazard has been reported in Mahaveer Nagar Extension, Kota, Rajasthan. The issue appears to be related to general untidiness and scattered waste, contributing to unhygienic conditions and potential health concerns. The citizen, Jaykishan Rawat, has requested that this matter be addressed by the Municipal Commissioner.\\\\\\",\\\\n  \\\\\\"petition_draft\\\\\\": \\\\\\"To,\\\\\\\\nThe Municipal Commissioner,\\\\\\\\nKota Municipal Corporation,\\\\\\\\nKota, Rajasthan.\\\\\\\\n\\\\\\\\nSubject: Urgent attention required for a Civic Safety and Sanitation Hazard in Mahaveer Nagar Extension.\\\\\\\\n\\\\\\\\nRespected Sir\\/Madam,\\\\\\\\n\\\\\\\\nI am writing to bring to your immediate attention a significant civic safety and sanitation hazard observed at the location 1\\/K-43, Mahaveer Nagar Extension, Mahaveer Nagar, Kota, Rajasthan 324009. The area is experiencing issues with scattered waste and general untidiness, which contributes to unhygienic conditions and poses a potential health risk to residents.\\\\\\\\n\\\\\\\\nWhile the exact nature of the hazard is currently unidentified, the presence of accumulated litter and lack of cleanliness is a matter of concern for public well-being and environmental hygiene in our locality. We kindly request your intervention to investigate this matter thoroughly and implement necessary measures to clean up the area\\"\\n          }\\n        ],\\n        \\"role\\": \\"model\\"\\n      },\\n      \\"finishReason\\": \\"MAX_TOKENS\\",\\n      \\"index\\": 0\\n    }\\n  ],\\n  \\"usageMetadata\\": {\\n    \\"promptTokenCount\\": 438,\\n    \\"candidatesTokenCount\\": 307,\\n    \\"totalTokenCount\\": 2472,\\n    \\"promptTokensDetails\\": [\\n      {\\n        \\"modality\\": \\"TEXT\\",\\n        \\"tokenCount\\": 180\\n      },\\n      {\\n        \\"modality\\": \\"IMAGE\\",\\n        \\"tokenCount\\": 258\\n      }\\n    ],\\n    \\"thoughtsTokenCount\\": 1727,\\n    \\"serviceTier\\": \\"standard\\"\\n  },\\n  \\"modelVersion\\": \\"gemini-2.5-flash\\",\\n  \\"responseId\\": \\"YNI-aq2wLoDTjuMP0tPpwQ0\\"\\n}\\n"}',
      'created_at' => '2026-06-26 19:26:38',
      'updated_at' => '2026-06-26 19:26:38',
    ),
    1 => 
    array (
      'id' => 2,
      'hazard_id' => NULL,
      'generated_summary' => 'The reported hazard appears to be a sanitation issue involving significant clutter and scattered waste materials on the floor, potentially leading to unhygienic conditions or attracting pests. While the specific nature of the \'civic safety hazard\' is unidentified by the user, the visual evidence suggests a need for waste management and cleanup.',
      'petition_draft' => 'To,
The Municipal Commissioner,
Kota Municipal Corporation,
Kota, Rajasthan.

Subject: Urgent attention required for a civic safety and sanitation hazard at 2/F-61, Vistar Yojna, Mahaveer Nagar Extension.

Dear Sir/Madam,

I am writing to bring to your immediate attention a significant civic safety and sanitation hazard observed at the premises located at 2/F-61, Vistar Yojna, Mahaveer Nagar Extension, Mahaveer Nagar, Kota, Rajasthan 324009.

The area in question exhibits considerable accumulation of clutter, scattered waste materials, and general disarray on the floor. While the exact nature of the civic safety hazard remains unidentified, such conditions pose potential risks including unhygienic environments, attraction of pests, and potential fire hazards, thereby impacting the overall safety and well-being of the residents in the vicinity.

I kindly request your office to investigate this matter promptly and take necessary actions to ensure the cleanup and proper waste management at the aforementioned location. Your swift intervention in resolving this sanitation issue would be highly appreciated.

Thank you for your time and consideration.

Sincerely,
Jaykishan Rawat',
      'predicted_severity' => 'Moderate',
      'severity_reasoning' => NULL,
      'is_duplicate' => 0,
      'duplicate_of_id' => NULL,
      'raw_payload' => '{"predicted_category":"Sanitation Hazard","predicted_severity":"Moderate","confidence_score":0.85,"generated_summary":"The reported hazard appears to be a sanitation issue involving significant clutter and scattered waste materials on the floor, potentially leading to unhygienic conditions or attracting pests. While the specific nature of the \'civic safety hazard\' is unidentified by the user, the visual evidence suggests a need for waste management and cleanup.","petition_draft":"To,\\nThe Municipal Commissioner,\\nKota Municipal Corporation,\\nKota, Rajasthan.\\n\\nSubject: Urgent attention required for a civic safety and sanitation hazard at 2\\/F-61, Vistar Yojna, Mahaveer Nagar Extension.\\n\\nDear Sir\\/Madam,\\n\\nI am writing to bring to your immediate attention a significant civic safety and sanitation hazard observed at the premises located at 2\\/F-61, Vistar Yojna, Mahaveer Nagar Extension, Mahaveer Nagar, Kota, Rajasthan 324009.\\n\\nThe area in question exhibits considerable accumulation of clutter, scattered waste materials, and general disarray on the floor. While the exact nature of the civic safety hazard remains unidentified, such conditions pose potential risks including unhygienic environments, attraction of pests, and potential fire hazards, thereby impacting the overall safety and well-being of the residents in the vicinity.\\n\\nI kindly request your office to investigate this matter promptly and take necessary actions to ensure the cleanup and proper waste management at the aforementioned location. Your swift intervention in resolving this sanitation issue would be highly appreciated.\\n\\nThank you for your time and consideration.\\n\\nSincerely,\\nJaykishan Rawat","status":"Success"}',
      'created_at' => '2026-06-26 19:28:19',
      'updated_at' => '2026-06-26 19:28:19',
    ),
    2 => 
    array (
      'id' => 3,
      'hazard_id' => NULL,
      'generated_summary' => 'An unidentified civic safety hazard, specifically an obstruction or clutter, has been reported at 1/K-43, Mahaveer Nagar Extension, Mahaveer Nagar, Kota, Rajasthan. The nature of the hazard requires further investigation by civic authorities.',
      'petition_draft' => 'To,
The Municipal Commissioner,
Kota Municipal Corporation,
Kota, Rajasthan.

Subject: Urgent attention required for an unidentified civic safety hazard at Mahaveer Nagar Extension.

Dear Sir/Madam,

I am writing to report an unidentified civic safety hazard located at 1/K-43, Mahaveer Nagar Extension, Mahaveer Nagar, Kota, Rajasthan 324009. The hazard, which appears to involve significant clutter and obstruction, poses a potential risk to public safety and accessibility. The coordinates for this location are 25.1387117, 75.8322506.

I urge your office to investigate this matter promptly and take the necessary actions to mitigate the risk and ensure the safety of the residents in the area. Thank you for your immediate attention to this important civic concern.

Sincerely,
Jaykishan Rawat',
      'predicted_severity' => 'Moderate',
      'severity_reasoning' => NULL,
      'is_duplicate' => 0,
      'duplicate_of_id' => NULL,
      'raw_payload' => '{"predicted_category":"Obstruction\\/Clutter","predicted_severity":"Moderate","confidence_score":0.7,"generated_summary":"An unidentified civic safety hazard, specifically an obstruction or clutter, has been reported at 1\\/K-43, Mahaveer Nagar Extension, Mahaveer Nagar, Kota, Rajasthan. The nature of the hazard requires further investigation by civic authorities.","petition_draft":"To,\\nThe Municipal Commissioner,\\nKota Municipal Corporation,\\nKota, Rajasthan.\\n\\nSubject: Urgent attention required for an unidentified civic safety hazard at Mahaveer Nagar Extension.\\n\\nDear Sir\\/Madam,\\n\\nI am writing to report an unidentified civic safety hazard located at 1\\/K-43, Mahaveer Nagar Extension, Mahaveer Nagar, Kota, Rajasthan 324009. The hazard, which appears to involve significant clutter and obstruction, poses a potential risk to public safety and accessibility. The coordinates for this location are 25.1387117, 75.8322506.\\n\\nI urge your office to investigate this matter promptly and take the necessary actions to mitigate the risk and ensure the safety of the residents in the area. Thank you for your immediate attention to this important civic concern.\\n\\nSincerely,\\nJaykishan Rawat","status":"Success"}',
      'created_at' => '2026-06-26 19:51:32',
      'updated_at' => '2026-06-26 19:51:32',
    ),
    3 => 
    array (
      'id' => 4,
      'hazard_id' => NULL,
      'generated_summary' => 'An unidentified civic safety hazard has been reported at coordinates 25.1387114, 75.8322505, specifically in the Mahaveer Nagar Extension area of Kota, Rajasthan. The nature of the hazard is not specified in the user\'s description or the provided image, which appears to be unrelated to any civic issue. Further investigation is required to determine the exact nature and severity of the reported issue.',
      'petition_draft' => 'To,
The Municipal Commissioner,
Kota Municipal Corporation,
Kota, Rajasthan

Subject: Report of Unidentified Civic Safety Hazard at 1/K-43, Mahaveer Nagar Extension

Dear Sir/Madam,

I am writing to bring to your urgent attention an unidentified civic safety hazard reported in the area of 1/K-43, Mahaveer Nagar Extension, Mahaveer Nagar, Kota, Rajasthan 324009. The exact nature of this hazard is currently unknown, but it has been identified as a potential risk to public safety.

I kindly request that your office dispatch a team to investigate this matter thoroughly at the aforementioned location (coordinates: 25.1387114, 75.8322505) and take appropriate measures to identify and mitigate any existing safety concerns.

Your prompt attention to this matter would be greatly appreciated in ensuring the safety and well-being of the residents in Mahaveer Nagar Extension.

Sincerely,
Jaykishan Rawat',
      'predicted_severity' => 'Unknown',
      'severity_reasoning' => NULL,
      'is_duplicate' => 0,
      'duplicate_of_id' => NULL,
      'raw_payload' => '{"predicted_category":"Unidentified Civic Safety Hazard","predicted_severity":"Unknown","confidence_score":0.35,"generated_summary":"An unidentified civic safety hazard has been reported at coordinates 25.1387114, 75.8322505, specifically in the Mahaveer Nagar Extension area of Kota, Rajasthan. The nature of the hazard is not specified in the user\'s description or the provided image, which appears to be unrelated to any civic issue. Further investigation is required to determine the exact nature and severity of the reported issue.","petition_draft":"To,\\nThe Municipal Commissioner,\\nKota Municipal Corporation,\\nKota, Rajasthan\\n\\nSubject: Report of Unidentified Civic Safety Hazard at 1\\/K-43, Mahaveer Nagar Extension\\n\\nDear Sir\\/Madam,\\n\\nI am writing to bring to your urgent attention an unidentified civic safety hazard reported in the area of 1\\/K-43, Mahaveer Nagar Extension, Mahaveer Nagar, Kota, Rajasthan 324009. The exact nature of this hazard is currently unknown, but it has been identified as a potential risk to public safety.\\n\\nI kindly request that your office dispatch a team to investigate this matter thoroughly at the aforementioned location (coordinates: 25.1387114, 75.8322505) and take appropriate measures to identify and mitigate any existing safety concerns.\\n\\nYour prompt attention to this matter would be greatly appreciated in ensuring the safety and well-being of the residents in Mahaveer Nagar Extension.\\n\\nSincerely,\\nJaykishan Rawat","status":"Success"}',
      'created_at' => '2026-06-26 21:06:37',
      'updated_at' => '2026-06-26 21:06:37',
    ),
    4 => 
    array (
      'id' => 5,
      'hazard_id' => NULL,
      'generated_summary' => 'An unidentified civic safety hazard has been reported by Jaykishan Rawat at 1/K-43, Mahaveer Nagar Extension, Mahaveer Nagar, Kota, Rajasthan. The visual evidence includes waste bins, suggesting a potential issue related to waste management or sanitation that requires investigation.',
      'petition_draft' => 'To,
The Municipal Commissioner,
Kota Municipal Corporation,
Kota, Rajasthan.

Subject: Urgent Report of Unidentified Civic Safety Hazard at 1/K-43, Mahaveer Nagar Extension, Mahaveer Nagar, Kota.

Dear Sir/Madam,

I am writing to bring to your immediate attention an unidentified civic safety hazard observed at the location 1/K-43, Mahaveer Nagar Extension, Mahaveer Nagar, Kota, Rajasthan 324009. The exact nature of the hazard is not fully clear from the visual evidence, which includes the presence of waste bins. However, there is a concern that this situation may pose a risk to public health and safety, potentially related to improper waste management or accumulation.

I kindly request your office to dispatch a team to investigate this matter thoroughly at the earliest convenience and take necessary remedial actions to ensure the safety and well-being of the residents in the area.

Thank you for your prompt attention to this urgent civic matter.

Sincerely,
Jaykishan Rawat',
      'predicted_severity' => 'Moderate',
      'severity_reasoning' => NULL,
      'is_duplicate' => 0,
      'duplicate_of_id' => NULL,
      'raw_payload' => '{"predicted_category":"Waste Management \\/ Sanitation","predicted_severity":"Moderate","confidence_score":0.75,"generated_summary":"An unidentified civic safety hazard has been reported by Jaykishan Rawat at 1\\/K-43, Mahaveer Nagar Extension, Mahaveer Nagar, Kota, Rajasthan. The visual evidence includes waste bins, suggesting a potential issue related to waste management or sanitation that requires investigation.","petition_draft":"To,\\nThe Municipal Commissioner,\\nKota Municipal Corporation,\\nKota, Rajasthan.\\n\\nSubject: Urgent Report of Unidentified Civic Safety Hazard at 1\\/K-43, Mahaveer Nagar Extension, Mahaveer Nagar, Kota.\\n\\nDear Sir\\/Madam,\\n\\nI am writing to bring to your immediate attention an unidentified civic safety hazard observed at the location 1\\/K-43, Mahaveer Nagar Extension, Mahaveer Nagar, Kota, Rajasthan 324009. The exact nature of the hazard is not fully clear from the visual evidence, which includes the presence of waste bins. However, there is a concern that this situation may pose a risk to public health and safety, potentially related to improper waste management or accumulation.\\n\\nI kindly request your office to dispatch a team to investigate this matter thoroughly at the earliest convenience and take necessary remedial actions to ensure the safety and well-being of the residents in the area.\\n\\nThank you for your prompt attention to this urgent civic matter.\\n\\nSincerely,\\nJaykishan Rawat","status":"Success"}',
      'created_at' => '2026-06-26 21:14:56',
      'updated_at' => '2026-06-26 21:14:56',
    ),
    5 => 
    array (
      'id' => 6,
      'hazard_id' => NULL,
      'generated_summary' => 'The reported \'unidentified civic safety hazard\' could not be confirmed based on the provided image, which depicts a domestic rabbit on a bed. No civic safety hazard is apparent from the visual evidence.',
      'petition_draft' => 'To,
The Municipal Commissioner,
Kota Municipal Corporation,
Kota, Rajasthan, India.

Subject: Clarification Regarding a Reported Unidentified Civic Safety Hazard at 1/K-43, Mahaveer Nagar Extension

Dear Sir/Madam,

This letter refers to a report of an "unidentified civic safety hazard" submitted by me, Jaykishan Rawat, concerning the location 1/K-43, Mahaveer Nagar Extension, Mahaveer Nagar, Kota, Rajasthan 324009. The coordinates provided are 25.1387112, 75.8322504.

Upon review of the accompanying details, specifically the image provided, it appears that the visual evidence depicts a domestic rabbit on a bed, which does not correspond to a civic safety hazard.

I am writing to clarify that based on the visual information, a specific civic safety hazard could not be identified. If there was an intention to report a different issue, kindly provide further details or a more relevant image.

Thank you for your attention to this matter.

Sincerely,
Jaykishan Rawat',
      'predicted_severity' => 'None',
      'severity_reasoning' => NULL,
      'is_duplicate' => 0,
      'duplicate_of_id' => NULL,
      'raw_payload' => '{"predicted_category":"No Hazard Identified","predicted_severity":"None","confidence_score":0.98,"generated_summary":"The reported \'unidentified civic safety hazard\' could not be confirmed based on the provided image, which depicts a domestic rabbit on a bed. No civic safety hazard is apparent from the visual evidence.","petition_draft":"To,\\nThe Municipal Commissioner,\\nKota Municipal Corporation,\\nKota, Rajasthan, India.\\n\\nSubject: Clarification Regarding a Reported Unidentified Civic Safety Hazard at 1\\/K-43, Mahaveer Nagar Extension\\n\\nDear Sir\\/Madam,\\n\\nThis letter refers to a report of an \\"unidentified civic safety hazard\\" submitted by me, Jaykishan Rawat, concerning the location 1\\/K-43, Mahaveer Nagar Extension, Mahaveer Nagar, Kota, Rajasthan 324009. The coordinates provided are 25.1387112, 75.8322504.\\n\\nUpon review of the accompanying details, specifically the image provided, it appears that the visual evidence depicts a domestic rabbit on a bed, which does not correspond to a civic safety hazard.\\n\\nI am writing to clarify that based on the visual information, a specific civic safety hazard could not be identified. If there was an intention to report a different issue, kindly provide further details or a more relevant image.\\n\\nThank you for your attention to this matter.\\n\\nSincerely,\\nJaykishan Rawat","status":"Success"}',
      'created_at' => '2026-06-26 21:27:09',
      'updated_at' => '2026-06-26 21:27:09',
    ),
    6 => 
    array (
      'id' => 7,
      'hazard_id' => NULL,
      'generated_summary' => 'A significant pothole, filled with water, has been identified on a road in Kota at coordinates 25.1387837, 75.8321511. This poses a civic safety hazard, potentially causing damage to vehicles and increasing the risk of accidents for drivers and pedestrians due to its size and obscured depth.',
      'petition_draft' => 'To,
The Municipal Commissioner,
Kota Municipal Corporation,
Kota.

Subject: Urgent attention required for a civic safety hazard (pothole) at 25.1387837, 75.8321511 in Kota.

Dear Sir/Madam,

I am writing to report a serious civic safety hazard in our city. A large and deep pothole, currently filled with water, is present on the road at the approximate coordinates 25.1387837, 75.8321511. This location falls within the jurisdiction of Kota.

This pothole poses a significant risk to public safety. It can cause severe damage to vehicles, including tires and suspension systems, and is a major accident hazard, especially for two-wheelers and at night when its depth is obscured by water. Pedestrians are also at risk of tripping and falling.

I urge your office to take immediate action to repair this dangerous road defect to ensure the safety of commuters and residents. Prompt intervention will prevent potential accidents and further inconvenience.

Thank you for your time and consideration.

Sincerely,
Jaykishan Rawat',
      'predicted_severity' => 'Moderate',
      'severity_reasoning' => NULL,
      'is_duplicate' => 0,
      'duplicate_of_id' => NULL,
      'raw_payload' => '{"predicted_category":"Road Hazard","predicted_severity":"Moderate","confidence_score":0.95,"generated_summary":"A significant pothole, filled with water, has been identified on a road in Kota at coordinates 25.1387837, 75.8321511. This poses a civic safety hazard, potentially causing damage to vehicles and increasing the risk of accidents for drivers and pedestrians due to its size and obscured depth.","petition_draft":"To,\\nThe Municipal Commissioner,\\nKota Municipal Corporation,\\nKota.\\n\\nSubject: Urgent attention required for a civic safety hazard (pothole) at 25.1387837, 75.8321511 in Kota.\\n\\nDear Sir\\/Madam,\\n\\nI am writing to report a serious civic safety hazard in our city. A large and deep pothole, currently filled with water, is present on the road at the approximate coordinates 25.1387837, 75.8321511. This location falls within the jurisdiction of Kota.\\n\\nThis pothole poses a significant risk to public safety. It can cause severe damage to vehicles, including tires and suspension systems, and is a major accident hazard, especially for two-wheelers and at night when its depth is obscured by water. Pedestrians are also at risk of tripping and falling.\\n\\nI urge your office to take immediate action to repair this dangerous road defect to ensure the safety of commuters and residents. Prompt intervention will prevent potential accidents and further inconvenience.\\n\\nThank you for your time and consideration.\\n\\nSincerely,\\nJaykishan Rawat","status":"Success"}',
      'created_at' => '2026-06-27 23:35:26',
      'updated_at' => '2026-06-27 23:35:26',
    ),
  ),
  'ai_settings' => 
  array (
    0 => 
    array (
      'id' => 1,
      'provider' => 'gemini',
      'api_key' => 'eyJpdiI6IjlURGpuck0wT1EwRmk4dkVCTHZDZlE9PSIsInZhbHVlIjoid05IZ01rOFZGaUZHSCtidmR5SExRRUFOS1YxU215bnNJUlhWd29DRHlkYWdrb3NkQTQzb0NWYUNTYitYMFV0eXRvcGZlQVFZTExJNmlPRER2RmNKd3c9PSIsIm1hYyI6IjUxMjgwYjUzNjE0MDMyOWNmZjhlZWUzNzc3NDgxZWI5ZTJkMDliYmQxZTM2YWE2Y2ZiM2ZkNTRjMjBmYjYwMTEiLCJ0YWciOiIifQ==',
      'model_name' => 'gemini-2.5-flash',
      'confidence_threshold' => '0.70',
      'classification_prompt' => 'Classify this hazard based on the provided details...',
      'auto_classification' => 1,
      'auto_severity_detection' => 1,
      'temperature' => '0.30',
      'max_tokens' => 2048,
      'created_at' => '2026-06-26 19:19:52',
      'updated_at' => '2026-06-26 19:20:18',
    ),
  ),
  'categories' => 
  array (
    0 => 
    array (
      'id' => 7,
      'name' => 'Pothole',
      'description' => 'Deformation or cavity in road surface.',
      'icon' => 'fa-road',
      'is_active' => 1,
      'created_at' => '2026-06-26 19:19:41',
      'updated_at' => '2026-06-26 19:19:41',
    ),
    1 => 
    array (
      'id' => 8,
      'name' => 'Open Drain',
      'description' => 'Uncovered roadside gutter drainage.',
      'icon' => 'fa-water',
      'is_active' => 1,
      'created_at' => '2026-06-26 19:19:41',
      'updated_at' => '2026-06-26 19:19:41',
    ),
    2 => 
    array (
      'id' => 9,
      'name' => 'Open Manhole',
      'description' => 'Missing utility cover on main pathway.',
      'icon' => 'fa-circle-dot',
      'is_active' => 1,
      'created_at' => '2026-06-26 19:19:41',
      'updated_at' => '2026-06-26 19:19:41',
    ),
    3 => 
    array (
      'id' => 10,
      'name' => 'Waterlogging',
      'description' => 'Flooded road section blocking traffic.',
      'icon' => 'fa-cloud-showers-heavy',
      'is_active' => 1,
      'created_at' => '2026-06-26 19:19:41',
      'updated_at' => '2026-06-26 19:19:41',
    ),
    4 => 
    array (
      'id' => 11,
      'name' => 'Broken Streetlight',
      'description' => 'Outage causing dark zones on road stretches.',
      'icon' => 'fa-lightbulb',
      'is_active' => 1,
      'created_at' => '2026-06-26 19:19:41',
      'updated_at' => '2026-06-26 19:19:41',
    ),
    5 => 
    array (
      'id' => 12,
      'name' => 'Garbage',
      'description' => 'Public trash pile blocking routes.',
      'icon' => 'fa-trash',
      'is_active' => 1,
      'created_at' => '2026-06-26 19:19:41',
      'updated_at' => '2026-06-26 19:19:41',
    ),
  ),
  'settings' => 
  array (
    0 => 
    array (
      'id' => 10,
      'key' => 'app_name',
      'value' => 'NAGAR RAKSHAK',
      'created_at' => '2026-06-26 19:19:41',
      'updated_at' => '2026-06-29 01:36:19',
    ),
    1 => 
    array (
      'id' => 11,
      'key' => 'contact_email',
      'value' => 'support@nagarrakshak.org',
      'created_at' => '2026-06-26 19:19:41',
      'updated_at' => '2026-06-26 19:19:41',
    ),
    2 => 
    array (
      'id' => 12,
      'key' => 'alert_radius',
      'value' => '500',
      'created_at' => '2026-06-26 19:19:41',
      'updated_at' => '2026-06-29 03:21:16',
    ),
    3 => 
    array (
      'id' => 13,
      'key' => 'critical_threshold',
      'value' => '10',
      'created_at' => '2026-06-26 19:19:41',
      'updated_at' => '2026-06-26 19:19:41',
    ),
    4 => 
    array (
      'id' => 14,
      'key' => 'auto_escalation',
      'value' => '0',
      'created_at' => '2026-06-26 19:19:41',
      'updated_at' => '2026-06-29 01:15:03',
    ),
    5 => 
    array (
      'id' => 15,
      'key' => 'confidence_threshold',
      'value' => '0.7',
      'created_at' => '2026-06-26 19:19:41',
      'updated_at' => '2026-06-26 19:19:41',
    ),
    6 => 
    array (
      'id' => 16,
      'key' => 'auto_classification',
      'value' => '1',
      'created_at' => '2026-06-26 19:19:41',
      'updated_at' => '2026-06-26 19:19:41',
    ),
    7 => 
    array (
      'id' => 17,
      'key' => 'auto_severity_detection',
      'value' => '1',
      'created_at' => '2026-06-26 19:19:41',
      'updated_at' => '2026-06-26 19:19:41',
    ),
    8 => 
    array (
      'id' => 18,
      'key' => 'classification_prompt',
      'value' => 'Analyze this image and classify any public safety hazard.',
      'created_at' => '2026-06-26 19:19:41',
      'updated_at' => '2026-06-26 19:19:41',
    ),
    9 => 
    array (
      'id' => 19,
      'key' => 'google_maps_api_key',
      'value' => '',
      'created_at' => '2026-06-26 19:20:18',
      'updated_at' => '2026-06-29 01:38:25',
    ),
    10 => 
    array (
      'id' => 20,
      'key' => 'gemini_api_key',
      'value' => '',
      'created_at' => '2026-06-26 19:20:18',
      'updated_at' => '2026-06-26 19:20:18',
    ),
    11 => 
    array (
      'id' => 21,
      'key' => 'gcs_bucket_name',
      'value' => 'nagarakshak',
      'created_at' => '2026-06-26 19:20:18',
      'updated_at' => '2026-06-26 19:20:18',
    ),
    12 => 
    array (
      'id' => 22,
      'key' => 'gcs_key_file',
      'value' => '',
      'created_at' => '2026-06-26 19:20:18',
      'updated_at' => '2026-06-26 21:11:44',
    ),
    13 => 
    array (
      'id' => 23,
      'key' => 'logo_path',
      'value' => NULL,
      'created_at' => '2026-06-26 21:05:31',
      'updated_at' => '2026-06-26 21:05:31',
    ),
    14 => 
    array (
      'id' => 24,
      'key' => 'gemini_analysis_enabled',
      'value' => '0',
      'created_at' => '2026-06-26 21:35:59',
      'updated_at' => '2026-06-28 13:18:56',
    ),
    15 => 
    array (
      'id' => 25,
      'key' => 'petition_enabled',
      'value' => '0',
      'created_at' => '2026-06-26 21:35:59',
      'updated_at' => '2026-06-28 13:18:57',
    ),
    16 => 
    array (
      'id' => 26,
      'key' => 'maintenance_mode',
      'value' => '0',
      'created_at' => '2026-06-28 11:43:08',
      'updated_at' => '2026-06-28 23:54:16',
    ),
    17 => 
    array (
      'id' => 27,
      'key' => 'app_version',
      'value' => '1.2.0',
      'created_at' => '2026-06-28 11:43:08',
      'updated_at' => '2026-06-28 11:54:56',
    ),
    18 => 
    array (
      'id' => 28,
      'key' => 'app_update_mandatory',
      'value' => '1',
      'created_at' => '2026-06-28 11:43:08',
      'updated_at' => '2026-06-28 11:43:43',
    ),
    19 => 
    array (
      'id' => 29,
      'key' => 'app_update_url',
      'value' => 'https://storage.googleapis.com/nagarakshak/app-release.apk',
      'created_at' => '2026-06-28 11:54:56',
      'updated_at' => '2026-06-29 03:47:28',
    ),
    20 => 
    array (
      'id' => 30,
      'key' => 'fcm_project_id',
      'value' => 'civicai-60cff',
      'created_at' => '2026-06-28 12:11:02',
      'updated_at' => '2026-06-28 12:11:02',
    ),
    21 => 
    array (
      'id' => 31,
      'key' => 'fcm_service_account',
      'value' => '',
      'created_at' => '2026-06-28 12:11:02',
      'updated_at' => '2026-06-28 12:11:02',
    ),
    22 => 
    array (
      'id' => 32,
      'key' => 'showcase_1_title',
      'value' => 'Biker Killed Hitting Unmarked Pothole on Aerodrome Road',
      'created_at' => '2026-06-29 02:57:46',
      'updated_at' => '2026-06-29 02:57:46',
    ),
    23 => 
    array (
      'id' => 33,
      'key' => 'showcase_1_location',
      'value' => 'Kota, Rajasthan',
      'created_at' => '2026-06-29 02:57:46',
      'updated_at' => '2026-06-29 02:57:46',
    ),
    24 => 
    array (
      'id' => 34,
      'key' => 'showcase_1_image',
      'value' => 'https://storage.googleapis.com/nagarakshak/hazards/showcase_1_1782701960.png',
      'created_at' => '2026-06-29 02:57:47',
      'updated_at' => '2026-06-29 02:59:21',
    ),
    25 => 
    array (
      'id' => 35,
      'key' => 'showcase_2_title',
      'value' => 'Three Killed as Car Plunges into Uncovered Municipal Drain',
      'created_at' => '2026-06-29 02:57:47',
      'updated_at' => '2026-06-29 02:57:47',
    ),
    26 => 
    array (
      'id' => 36,
      'key' => 'showcase_2_location',
      'value' => 'Pune, Maharashtra',
      'created_at' => '2026-06-29 02:57:47',
      'updated_at' => '2026-06-29 02:57:47',
    ),
    27 => 
    array (
      'id' => 37,
      'key' => 'showcase_3_title',
      'value' => '11 Accidents on Single Andheri Stretch in 30 Days',
      'created_at' => '2026-06-29 02:57:47',
      'updated_at' => '2026-06-29 02:57:47',
    ),
    28 => 
    array (
      'id' => 38,
      'key' => 'showcase_3_location',
      'value' => 'Mumbai, Maharashtra',
      'created_at' => '2026-06-29 02:57:47',
      'updated_at' => '2026-06-29 02:57:47',
    ),
    29 => 
    array (
      'id' => 39,
      'key' => 'showcase_4_title',
      'value' => 'BMTC Bus Partially Swallowed by Road Sinkhole on ORR',
      'created_at' => '2026-06-29 02:57:47',
      'updated_at' => '2026-06-29 02:57:47',
    ),
    30 => 
    array (
      'id' => 40,
      'key' => 'showcase_4_location',
      'value' => 'Bengaluru, Karnataka',
      'created_at' => '2026-06-29 02:57:47',
      'updated_at' => '2026-06-29 02:57:47',
    ),
    31 => 
    array (
      'id' => 41,
      'key' => 'showcase_5_title',
      'value' => 'Man Electrocuted Walking Through Flooded Underpass',
      'created_at' => '2026-06-29 02:57:47',
      'updated_at' => '2026-06-29 02:57:47',
    ),
    32 => 
    array (
      'id' => 42,
      'key' => 'showcase_5_location',
      'value' => 'New Delhi',
      'created_at' => '2026-06-29 02:57:47',
      'updated_at' => '2026-06-29 02:57:47',
    ),
    33 => 
    array (
      'id' => 43,
      'key' => 'showcase_6_title',
      'value' => '9-Year-Old Girl Falls into Drain Outside School',
      'created_at' => '2026-06-29 02:57:47',
      'updated_at' => '2026-06-29 02:57:47',
    ),
    34 => 
    array (
      'id' => 44,
      'key' => 'showcase_6_location',
      'value' => 'Lucknow, UP',
      'created_at' => '2026-06-29 02:57:47',
      'updated_at' => '2026-06-29 02:57:47',
    ),
    35 => 
    array (
      'id' => 45,
      'key' => 'showcase_2_image',
      'value' => 'https://storage.googleapis.com/nagarakshak/hazards/showcase_2_1782702327.jpg',
      'created_at' => '2026-06-29 03:05:27',
      'updated_at' => '2026-06-29 03:05:27',
    ),
    36 => 
    array (
      'id' => 46,
      'key' => 'showcase_4_image',
      'value' => 'https://storage.googleapis.com/nagarakshak/hazards/showcase_4_1782702328.webp',
      'created_at' => '2026-06-29 03:05:28',
      'updated_at' => '2026-06-29 03:05:28',
    ),
    37 => 
    array (
      'id' => 47,
      'key' => 'showcase_5_image',
      'value' => 'https://storage.googleapis.com/nagarakshak/hazards/showcase_5_1782702462.webp',
      'created_at' => '2026-06-29 03:07:43',
      'updated_at' => '2026-06-29 03:07:43',
    ),
    38 => 
    array (
      'id' => 48,
      'key' => 'showcase_6_image',
      'value' => 'https://storage.googleapis.com/nagarakshak/hazards/showcase_6_1782702463.avif',
      'created_at' => '2026-06-29 03:07:43',
      'updated_at' => '2026-06-29 03:07:43',
    ),
    39 => 
    array (
      'id' => 49,
      'key' => 'showcase_3_image',
      'value' => 'https://storage.googleapis.com/nagarakshak/hazards/showcase_3_1782702520.avif',
      'created_at' => '2026-06-29 03:08:40',
      'updated_at' => '2026-06-29 03:08:40',
    ),
  ),
  'users' => 
  array (
    0 => 
    array (
      'id' => 27,
      'name' => 'City Admin',
      'email' => 'admin@nagarrakshak.org',
      'email_verified_at' => NULL,
      'password' => '$2y$12$ILPsmcqDUsDEAlmUcA4Ib.7qwc1gJULMsTfcaZQEh4m6/8rO5CieG',
      'reputation_score' => 0,
      'reports_submitted' => 0,
      'reports_verified' => 0,
      'badge_level' => 'Super Admin',
      'role' => 'City Admin',
      'remember_token' => 'Vlo2OrJS4yPxfcIfZbYUBEsiKWVLswSAzFaLBS9LW5m9t9RonLAYHpuXS9tQ',
      'created_at' => '2026-06-26 19:19:41',
      'updated_at' => '2026-06-26 19:41:25',
      'phone' => NULL,
      'two_factor_enabled' => 0,
      'aadhaar_number' => NULL,
      'id_card_verified' => 0,
      'email_notifications' => 1,
      'push_notifications' => 1,
      'hazard_alerts' => 1,
      'high_accuracy_location' => 1,
      'background_location' => 0,
      'offline_map_downloaded' => 0,
      'voice_alerts_enabled' => 1,
      'sound_alerts_enabled' => 1,
    ),
    1 => 
    array (
      'id' => 28,
      'name' => 'Aarav Sharma',
      'email' => 'aarav@nagarrakshak.org',
      'email_verified_at' => NULL,
      'password' => '$2y$12$oIs9M.K3Gxqlic3oCR.yd.ohtY5qvOPsEusFdmQzPOGyZ1AADZ/ua',
      'reputation_score' => 4820,
      'reports_submitted' => 32,
      'reports_verified' => 84,
      'badge_level' => 'Community Hero',
      'role' => 'Citizen',
      'remember_token' => NULL,
      'created_at' => '2026-06-26 19:19:42',
      'updated_at' => '2026-06-26 19:19:42',
      'phone' => NULL,
      'two_factor_enabled' => 0,
      'aadhaar_number' => NULL,
      'id_card_verified' => 0,
      'email_notifications' => 1,
      'push_notifications' => 1,
      'hazard_alerts' => 1,
      'high_accuracy_location' => 1,
      'background_location' => 0,
      'offline_map_downloaded' => 0,
      'voice_alerts_enabled' => 1,
      'sound_alerts_enabled' => 1,
    ),
    2 => 
    array (
      'id' => 29,
      'name' => 'Priya Patel',
      'email' => 'priya@nagarrakshak.org',
      'email_verified_at' => NULL,
      'password' => '$2y$12$YJFxNNK4gr4Gf/9KC2t40Ooi7rUMSGNTRpg449jjTMqtrir.OLsKG',
      'reputation_score' => 3950,
      'reports_submitted' => 25,
      'reports_verified' => 67,
      'badge_level' => 'Community Hero',
      'role' => 'Citizen',
      'remember_token' => NULL,
      'created_at' => '2026-06-26 19:19:42',
      'updated_at' => '2026-06-26 19:19:42',
      'phone' => NULL,
      'two_factor_enabled' => 0,
      'aadhaar_number' => NULL,
      'id_card_verified' => 0,
      'email_notifications' => 1,
      'push_notifications' => 1,
      'hazard_alerts' => 1,
      'high_accuracy_location' => 1,
      'background_location' => 0,
      'offline_map_downloaded' => 0,
      'voice_alerts_enabled' => 1,
      'sound_alerts_enabled' => 1,
    ),
    3 => 
    array (
      'id' => 30,
      'name' => 'Rohan Verma',
      'email' => 'rohan@nagarrakshak.org',
      'email_verified_at' => NULL,
      'password' => '$2y$12$FqtwqwTVOBLpxwYBtE2tpenmuTz9VDMjdw0nFFa4II9cvi2BnBOWO',
      'reputation_score' => 3210,
      'reports_submitted' => 19,
      'reports_verified' => 45,
      'badge_level' => 'Civic Champion',
      'role' => 'Citizen',
      'remember_token' => NULL,
      'created_at' => '2026-06-26 19:19:42',
      'updated_at' => '2026-06-26 19:19:42',
      'phone' => NULL,
      'two_factor_enabled' => 0,
      'aadhaar_number' => NULL,
      'id_card_verified' => 0,
      'email_notifications' => 1,
      'push_notifications' => 1,
      'hazard_alerts' => 1,
      'high_accuracy_location' => 1,
      'background_location' => 0,
      'offline_map_downloaded' => 0,
      'voice_alerts_enabled' => 1,
      'sound_alerts_enabled' => 1,
    ),
    4 => 
    array (
      'id' => 31,
      'name' => 'Mihir Aditya',
      'email' => 'mihir.aditya@gmail.com',
      'email_verified_at' => NULL,
      'password' => '$2y$12$bN7AUKvKNn8wNyhXqqRU8Oobl0lfwU0qala/LcPO34tg4nL38dufO',
      'reputation_score' => 0,
      'reports_submitted' => 0,
      'reports_verified' => 0,
      'badge_level' => 'Contributor',
      'role' => 'Citizen',
      'remember_token' => '6bD52Q10HdQrpyBXOBAewM6rX8IlJXRsRd9noMd1ztN27GyrVHKw2YEnlAsd',
      'created_at' => '2026-06-26 19:36:13',
      'updated_at' => '2026-06-26 19:36:13',
      'phone' => NULL,
      'two_factor_enabled' => 0,
      'aadhaar_number' => NULL,
      'id_card_verified' => 0,
      'email_notifications' => 1,
      'push_notifications' => 1,
      'hazard_alerts' => 1,
      'high_accuracy_location' => 1,
      'background_location' => 0,
      'offline_map_downloaded' => 0,
      'voice_alerts_enabled' => 1,
      'sound_alerts_enabled' => 1,
    ),
    5 => 
    array (
      'id' => 32,
      'name' => 'Jaykishan Rawat',
      'email' => 'jksonu1436@gmail.com',
      'email_verified_at' => NULL,
      'password' => '$2y$12$7mhOVdYPRNrPGwZUgbXMj.ole4x1StAoz9LkM0VA5T5IL5VHDxuTC',
      'reputation_score' => 740,
      'reports_submitted' => 7,
      'reports_verified' => 2,
      'badge_level' => 'Civic Guardian',
      'role' => 'Citizen',
      'remember_token' => 'w8opNcKsrc4pxBIXG6hPdYxYoB7st3XD7QFh9L3JvoycNGhXnhvV5OBxHeFR',
      'created_at' => '2026-06-26 21:13:54',
      'updated_at' => '2026-06-29 03:20:48',
      'phone' => NULL,
      'two_factor_enabled' => 0,
      'aadhaar_number' => NULL,
      'id_card_verified' => 0,
      'email_notifications' => 1,
      'push_notifications' => 1,
      'hazard_alerts' => 1,
      'high_accuracy_location' => 1,
      'background_location' => 0,
      'offline_map_downloaded' => 0,
      'voice_alerts_enabled' => 1,
      'sound_alerts_enabled' => 1,
    ),
    6 => 
    array (
      'id' => 33,
      'name' => 'Golu Kumar',
      'email' => 'arvindkumarnk62053@gmail.com',
      'email_verified_at' => NULL,
      'password' => '$2y$12$MKCHhAEPn.ASTGv0MFPJcOuGkIkOHx9CsfcoZfREWAr4CZBbIWL1O',
      'reputation_score' => 0,
      'reports_submitted' => 0,
      'reports_verified' => 0,
      'badge_level' => 'Contributor',
      'role' => 'Citizen',
      'remember_token' => 'Clj5mYFevwfFpVh8SzsaKJMF9vekW4Vb24h9M7Z8CL4RnuKwNVqShAo8B4fX',
      'created_at' => '2026-06-29 02:21:38',
      'updated_at' => '2026-06-29 03:45:27',
      'phone' => '9608009595',
      'two_factor_enabled' => 1,
      'aadhaar_number' => '731097963295',
      'id_card_verified' => 1,
      'email_notifications' => 1,
      'push_notifications' => 1,
      'hazard_alerts' => 1,
      'high_accuracy_location' => 1,
      'background_location' => 0,
      'offline_map_downloaded' => 0,
      'voice_alerts_enabled' => 1,
      'sound_alerts_enabled' => 1,
    ),
  ),
  'activity_logs' => 
  array (
    0 => 
    array (
      'id' => 3,
      'user_id' => 28,
      'type' => 'User',
      'action' => 'Report Created',
      'description' => 'Reported a Pothole on Road at Talwandi, Kota.',
      'ip_address' => '127.0.0.1',
      'user_agent' => 'Mozilla/5.0',
      'created_at' => '2026-06-26 19:19:42',
      'updated_at' => '2026-06-26 19:19:42',
    ),
    1 => 
    array (
      'id' => 4,
      'user_id' => 27,
      'type' => 'Admin',
      'action' => 'Case Verified',
      'description' => 'Verified hazard case #2 (Open Drain at Sector 7, Kota).',
      'ip_address' => '127.0.0.1',
      'user_agent' => 'Mozilla/5.0',
      'created_at' => '2026-06-26 19:19:42',
      'updated_at' => '2026-06-26 19:19:42',
    ),
    2 => 
    array (
      'id' => 5,
      'user_id' => 27,
      'type' => 'Admin',
      'action' => 'Settings Updated',
      'description' => 'Updated core system brand configurations and Maps API.',
      'ip_address' => '10.69.249.93',
      'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36',
      'created_at' => '2026-06-26 21:05:31',
      'updated_at' => '2026-06-26 21:05:31',
    ),
    3 => 
    array (
      'id' => 6,
      'user_id' => 27,
      'type' => 'Admin',
      'action' => 'Settings Updated',
      'description' => 'Updated core system brand configurations and Maps API.',
      'ip_address' => '10.69.249.93',
      'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36',
      'created_at' => '2026-06-26 21:11:44',
      'updated_at' => '2026-06-26 21:11:44',
    ),
    4 => 
    array (
      'id' => 7,
      'user_id' => 27,
      'type' => 'Admin',
      'action' => 'Settings Updated',
      'description' => 'Updated alert configuration thresholds.',
      'ip_address' => '10.69.249.93',
      'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36',
      'created_at' => '2026-06-26 21:35:59',
      'updated_at' => '2026-06-26 21:35:59',
    ),
    5 => 
    array (
      'id' => 8,
      'user_id' => 27,
      'type' => 'Admin',
      'action' => 'Settings Updated',
      'description' => 'Updated alert configuration thresholds.',
      'ip_address' => '10.53.211.93',
      'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36',
      'created_at' => '2026-06-27 23:04:09',
      'updated_at' => '2026-06-27 23:04:09',
    ),
    6 => 
    array (
      'id' => 9,
      'user_id' => 27,
      'type' => 'Admin',
      'action' => 'Settings Updated',
      'description' => 'Updated Maintenance mode and App Update configuration settings.',
      'ip_address' => '127.0.0.1',
      'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36',
      'created_at' => '2026-06-28 11:43:08',
      'updated_at' => '2026-06-28 11:43:08',
    ),
    7 => 
    array (
      'id' => 10,
      'user_id' => 27,
      'type' => 'Admin',
      'action' => 'Settings Updated',
      'description' => 'Updated Maintenance mode and App Update configuration settings.',
      'ip_address' => '127.0.0.1',
      'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36',
      'created_at' => '2026-06-28 11:43:43',
      'updated_at' => '2026-06-28 11:43:43',
    ),
    8 => 
    array (
      'id' => 11,
      'user_id' => 27,
      'type' => 'Admin',
      'action' => 'Settings Updated',
      'description' => 'Updated Maintenance mode and App Update configuration settings.',
      'ip_address' => '127.0.0.1',
      'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36',
      'created_at' => '2026-06-28 11:43:58',
      'updated_at' => '2026-06-28 11:43:58',
    ),
    9 => 
    array (
      'id' => 12,
      'user_id' => 27,
      'type' => 'Admin',
      'action' => 'Settings Updated',
      'description' => 'Updated Maintenance mode and App Update configuration settings.',
      'ip_address' => '127.0.0.1',
      'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36',
      'created_at' => '2026-06-28 11:54:56',
      'updated_at' => '2026-06-28 11:54:56',
    ),
    10 => 
    array (
      'id' => 13,
      'user_id' => 27,
      'type' => 'Admin',
      'action' => 'Settings Updated',
      'description' => 'Updated Maintenance mode and App Update configuration settings.',
      'ip_address' => '127.0.0.1',
      'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36',
      'created_at' => '2026-06-28 11:55:15',
      'updated_at' => '2026-06-28 11:55:15',
    ),
    11 => 
    array (
      'id' => 14,
      'user_id' => 27,
      'type' => 'Admin',
      'action' => 'Settings Updated',
      'description' => 'Updated Maintenance mode and App Update configuration settings.',
      'ip_address' => '127.0.0.1',
      'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36',
      'created_at' => '2026-06-28 11:55:35',
      'updated_at' => '2026-06-28 11:55:35',
    ),
    12 => 
    array (
      'id' => 15,
      'user_id' => 27,
      'type' => 'Admin',
      'action' => 'Settings Updated',
      'description' => 'Updated core system brand configurations and Maps API.',
      'ip_address' => '127.0.0.1',
      'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36',
      'created_at' => '2026-06-28 12:11:02',
      'updated_at' => '2026-06-28 12:11:02',
    ),
    13 => 
    array (
      'id' => 16,
      'user_id' => 27,
      'type' => 'Admin',
      'action' => 'Notification Sent',
      'description' => 'Dispatched FCM Push notification campaign: \'High Alert\' to All Users.',
      'ip_address' => '127.0.0.1',
      'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36',
      'created_at' => '2026-06-28 12:12:09',
      'updated_at' => '2026-06-28 12:12:09',
    ),
    14 => 
    array (
      'id' => 17,
      'user_id' => 27,
      'type' => 'Admin',
      'action' => 'Notification Sent',
      'description' => 'Dispatched FCM Push notification campaign: \'⚠️ High Alert: Open Drain Detected\' to All Users.',
      'ip_address' => '127.0.0.1',
      'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36',
      'created_at' => '2026-06-28 12:16:27',
      'updated_at' => '2026-06-28 12:16:27',
    ),
    15 => 
    array (
      'id' => 18,
      'user_id' => 27,
      'type' => 'Admin',
      'action' => 'Notification Sent',
      'description' => 'Dispatched FCM Push notification campaign: \'e.g. ⚠️ High Alert: Open Drain Detected\' to All Users.',
      'ip_address' => '127.0.0.1',
      'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36',
      'created_at' => '2026-06-28 12:24:43',
      'updated_at' => '2026-06-28 12:24:43',
    ),
    16 => 
    array (
      'id' => 19,
      'user_id' => 27,
      'type' => 'Admin',
      'action' => 'Settings Updated',
      'description' => 'Updated alert configuration thresholds.',
      'ip_address' => '127.0.0.1',
      'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36',
      'created_at' => '2026-06-28 13:18:57',
      'updated_at' => '2026-06-28 13:18:57',
    ),
    17 => 
    array (
      'id' => 20,
      'user_id' => 27,
      'type' => 'Admin',
      'action' => 'Settings Updated',
      'description' => 'Updated Maintenance mode and App Update configuration settings.',
      'ip_address' => '127.0.0.1',
      'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36',
      'created_at' => '2026-06-28 23:51:20',
      'updated_at' => '2026-06-28 23:51:20',
    ),
    18 => 
    array (
      'id' => 21,
      'user_id' => 27,
      'type' => 'Admin',
      'action' => 'Settings Updated',
      'description' => 'Updated Maintenance mode and App Update configuration settings.',
      'ip_address' => '127.0.0.1',
      'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36',
      'created_at' => '2026-06-28 23:54:16',
      'updated_at' => '2026-06-28 23:54:16',
    ),
    19 => 
    array (
      'id' => 22,
      'user_id' => 27,
      'type' => 'Admin',
      'action' => 'Case Rejected',
      'description' => 'Rejected hazard case #21 as false report.',
      'ip_address' => '127.0.0.1',
      'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36',
      'created_at' => '2026-06-28 23:58:53',
      'updated_at' => '2026-06-28 23:58:53',
    ),
    20 => 
    array (
      'id' => 23,
      'user_id' => 27,
      'type' => 'Admin',
      'action' => 'Category Created',
      'description' => 'Created new hazard category: \'Jaykishan Rawat\'',
      'ip_address' => '127.0.0.1',
      'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36',
      'created_at' => '2026-06-29 00:09:28',
      'updated_at' => '2026-06-29 00:09:28',
    ),
    21 => 
    array (
      'id' => 24,
      'user_id' => 27,
      'type' => 'Admin',
      'action' => 'Category Deleted',
      'description' => 'Deleted hazard category: \'Jaykishan Rawat\'',
      'ip_address' => '127.0.0.1',
      'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36',
      'created_at' => '2026-06-29 00:09:53',
      'updated_at' => '2026-06-29 00:09:53',
    ),
    22 => 
    array (
      'id' => 25,
      'user_id' => 27,
      'type' => 'Admin',
      'action' => 'Settings Updated',
      'description' => 'Updated alert configuration thresholds.',
      'ip_address' => '127.0.0.1',
      'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36',
      'created_at' => '2026-06-29 01:15:04',
      'updated_at' => '2026-06-29 01:15:04',
    ),
    23 => 
    array (
      'id' => 26,
      'user_id' => 27,
      'type' => 'Admin',
      'action' => 'Settings Updated',
      'description' => 'Updated core system brand configurations and Maps API.',
      'ip_address' => '127.0.0.1',
      'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36',
      'created_at' => '2026-06-29 01:36:19',
      'updated_at' => '2026-06-29 01:36:19',
    ),
    24 => 
    array (
      'id' => 27,
      'user_id' => 27,
      'type' => 'Admin',
      'action' => 'Settings Updated',
      'description' => 'Updated core system brand configurations and Maps API.',
      'ip_address' => '127.0.0.1',
      'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36',
      'created_at' => '2026-06-29 01:38:25',
      'updated_at' => '2026-06-29 01:38:25',
    ),
    25 => 
    array (
      'id' => 28,
      'user_id' => 27,
      'type' => 'Admin',
      'action' => 'Settings Updated',
      'description' => 'Updated alert configuration thresholds.',
      'ip_address' => '127.0.0.1',
      'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36',
      'created_at' => '2026-06-29 02:31:56',
      'updated_at' => '2026-06-29 02:31:56',
    ),
    26 => 
    array (
      'id' => 29,
      'user_id' => 27,
      'type' => 'Admin',
      'action' => 'Settings Updated',
      'description' => 'Updated alert configuration thresholds.',
      'ip_address' => '127.0.0.1',
      'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36',
      'created_at' => '2026-06-29 02:32:26',
      'updated_at' => '2026-06-29 02:32:26',
    ),
    27 => 
    array (
      'id' => 30,
      'user_id' => 27,
      'type' => 'Admin',
      'action' => 'Notification Sent',
      'description' => 'Dispatched FCM Push notification campaign: \'hh\' to All Users.',
      'ip_address' => '127.0.0.1',
      'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36',
      'created_at' => '2026-06-29 02:50:04',
      'updated_at' => '2026-06-29 02:50:04',
    ),
    28 => 
    array (
      'id' => 31,
      'user_id' => 27,
      'type' => 'Admin',
      'action' => 'Showcase Updated',
      'description' => 'Updated Hackathon homepage showcase images and incident details.',
      'ip_address' => '127.0.0.1',
      'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36',
      'created_at' => '2026-06-29 02:57:47',
      'updated_at' => '2026-06-29 02:57:47',
    ),
    29 => 
    array (
      'id' => 32,
      'user_id' => 27,
      'type' => 'Admin',
      'action' => 'Showcase Updated',
      'description' => 'Updated Hackathon homepage showcase images and incident details.',
      'ip_address' => '127.0.0.1',
      'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36',
      'created_at' => '2026-06-29 02:59:21',
      'updated_at' => '2026-06-29 02:59:21',
    ),
    30 => 
    array (
      'id' => 33,
      'user_id' => 27,
      'type' => 'Admin',
      'action' => 'Showcase Updated',
      'description' => 'Updated Hackathon homepage showcase images and incident details.',
      'ip_address' => '127.0.0.1',
      'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36',
      'created_at' => '2026-06-29 03:05:28',
      'updated_at' => '2026-06-29 03:05:28',
    ),
    31 => 
    array (
      'id' => 34,
      'user_id' => 27,
      'type' => 'Admin',
      'action' => 'Showcase Updated',
      'description' => 'Updated Hackathon homepage showcase images and incident details.',
      'ip_address' => '127.0.0.1',
      'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36',
      'created_at' => '2026-06-29 03:07:43',
      'updated_at' => '2026-06-29 03:07:43',
    ),
    32 => 
    array (
      'id' => 35,
      'user_id' => 27,
      'type' => 'Admin',
      'action' => 'Showcase Updated',
      'description' => 'Updated Hackathon homepage showcase images and incident details.',
      'ip_address' => '127.0.0.1',
      'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36',
      'created_at' => '2026-06-29 03:08:40',
      'updated_at' => '2026-06-29 03:08:40',
    ),
    33 => 
    array (
      'id' => 36,
      'user_id' => 27,
      'type' => 'Admin',
      'action' => 'Settings Updated',
      'description' => 'Updated alert configuration thresholds.',
      'ip_address' => '127.0.0.1',
      'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36',
      'created_at' => '2026-06-29 03:21:16',
      'updated_at' => '2026-06-29 03:21:16',
    ),
    34 => 
    array (
      'id' => 37,
      'user_id' => 27,
      'type' => 'Admin',
      'action' => 'Settings Updated',
      'description' => 'Uploaded new APK release to GCP Storage and updated App Version configurations.',
      'ip_address' => '127.0.0.1',
      'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36',
      'created_at' => '2026-06-29 03:47:28',
      'updated_at' => '2026-06-29 03:47:28',
    ),
  ),
  'hazards' => 
  array (
    0 => 
    array (
      'id' => 6,
      'category' => 'Pothole',
      'location_name' => 'Talwandi, Kota',
      'latitude' => 25.18254,
      'longitude' => 75.82736,
      'severity' => 'High Risk',
      'status' => 'Pending',
      'description' => NULL,
      'verification_count' => 14,
      'false_report_count' => 0,
      'resolution_votes' => 0,
      'is_archived' => 0,
      'confidence_score' => 0.94,
      'ai_severity_score' => 4,
      'ai_analysis_summary' => 'Gemini AI Analysis: Detected structural asphalt degradation. Confident classification: Pothole. Estimated severity: High Risk. Immediate repair recommended to avoid cyclist injury.',
      'image_path' => NULL,
      'created_by' => 28,
      'created_at' => '2026-06-26 19:19:42',
      'updated_at' => '2026-06-26 19:19:42',
    ),
    1 => 
    array (
      'id' => 7,
      'category' => 'Open Drain',
      'location_name' => 'Sector 7, Kota',
      'latitude' => 25.18421,
      'longitude' => 75.82912,
      'severity' => 'Critical',
      'status' => 'Verified',
      'description' => NULL,
      'verification_count' => 22,
      'false_report_count' => 1,
      'resolution_votes' => 3,
      'is_archived' => 0,
      'confidence_score' => 0.98,
      'ai_severity_score' => 5,
      'ai_analysis_summary' => 'Gemini AI Analysis: Detected open trench in pedestrian zone. Confident classification: Open Drain. Estimated severity: Critical. Recommended action: Municipality barricading.',
      'image_path' => NULL,
      'created_by' => 29,
      'created_at' => '2026-06-26 19:19:42',
      'updated_at' => '2026-06-26 19:19:42',
    ),
    2 => 
    array (
      'id' => 8,
      'category' => 'Waterlogging',
      'location_name' => 'Aerodrome Circle, Kota',
      'latitude' => 25.19532,
      'longitude' => 75.83541,
      'severity' => 'Medium Risk',
      'status' => 'Verified',
      'description' => NULL,
      'verification_count' => 8,
      'false_report_count' => 0,
      'resolution_votes' => 1,
      'is_archived' => 0,
      'confidence_score' => 0.89,
      'ai_severity_score' => 3,
      'ai_analysis_summary' => 'Gemini AI Analysis: Detected water accumulation on road surface. Confident classification: Waterlogging. Estimated severity: Medium Risk.',
      'image_path' => NULL,
      'created_by' => 30,
      'created_at' => '2026-06-26 19:19:42',
      'updated_at' => '2026-06-26 19:19:42',
    ),
    3 => 
    array (
      'id' => 9,
      'category' => 'Broken Streetlight',
      'location_name' => 'Kunadi, Kota',
      'latitude' => 25.21312,
      'longitude' => 75.84211,
      'severity' => 'Low Risk',
      'status' => 'Resolved',
      'description' => NULL,
      'verification_count' => 3,
      'false_report_count' => 0,
      'resolution_votes' => 8,
      'is_archived' => 0,
      'confidence_score' => 0.82,
      'ai_severity_score' => 2,
      'ai_analysis_summary' => 'Gemini AI Analysis: Detected lighting infrastructure outage. Confident classification: Broken Streetlight. Estimated severity: Low Risk.',
      'image_path' => NULL,
      'created_by' => 28,
      'created_at' => '2026-06-26 19:19:42',
      'updated_at' => '2026-06-26 19:19:42',
    ),
    4 => 
    array (
      'id' => 10,
      'category' => 'Pothole',
      'location_name' => 'Talwandi, Kota',
      'latitude' => 25.18254,
      'longitude' => 75.82736,
      'severity' => 'High Risk',
      'status' => 'Pending',
      'description' => 'Test description',
      'verification_count' => 0,
      'false_report_count' => 0,
      'resolution_votes' => 0,
      'is_archived' => 0,
      'confidence_score' => NULL,
      'ai_severity_score' => NULL,
      'ai_analysis_summary' => 'Test summary',
      'image_path' => 'http://10.69.249.93:8000/storage/hazards/test.webp',
      'created_by' => NULL,
      'created_at' => '2026-06-26 19:36:13',
      'updated_at' => '2026-06-26 19:36:13',
    ),
    5 => 
    array (
      'id' => 11,
      'category' => 'Pothole',
      'location_name' => 'Talwandi, Kota',
      'latitude' => 25.18254,
      'longitude' => 75.82736,
      'severity' => 'High Risk',
      'status' => 'Pending',
      'description' => 'Test description',
      'verification_count' => 0,
      'false_report_count' => 0,
      'resolution_votes' => 0,
      'is_archived' => 0,
      'confidence_score' => NULL,
      'ai_severity_score' => NULL,
      'ai_analysis_summary' => 'Test summary',
      'image_path' => 'http://10.69.249.93:8000/storage/hazards/test.webp',
      'created_by' => NULL,
      'created_at' => '2026-06-26 19:37:19',
      'updated_at' => '2026-06-26 19:37:19',
    ),
    6 => 
    array (
      'id' => 12,
      'category' => 'Garbage Dump',
      'location_name' => 'Talwandi, Kota (Guest Test)',
      'latitude' => 25.18254,
      'longitude' => 75.82736,
      'severity' => 'Medium Risk',
      'status' => 'Pending',
      'description' => 'Guest test - garbage dump near school',
      'verification_count' => 0,
      'false_report_count' => 0,
      'resolution_votes' => 0,
      'is_archived' => 0,
      'confidence_score' => NULL,
      'ai_severity_score' => NULL,
      'ai_analysis_summary' => 'Test guest summary',
      'image_path' => NULL,
      'created_by' => NULL,
      'created_at' => '2026-06-26 19:41:25',
      'updated_at' => '2026-06-26 19:41:25',
    ),
    7 => 
    array (
      'id' => 13,
      'category' => 'Pothole',
      'location_name' => 'Talwandi, Kota (Auth Test)',
      'latitude' => 25.18255,
      'longitude' => 75.82737,
      'severity' => 'High Risk',
      'status' => 'Pending',
      'description' => 'Auth test - pothole on main road',
      'verification_count' => 0,
      'false_report_count' => 0,
      'resolution_votes' => 0,
      'is_archived' => 0,
      'confidence_score' => NULL,
      'ai_severity_score' => NULL,
      'ai_analysis_summary' => 'Test auth summary',
      'image_path' => NULL,
      'created_by' => 27,
      'created_at' => '2026-06-26 19:41:25',
      'updated_at' => '2026-06-26 19:41:25',
    ),
    8 => 
    array (
      'id' => 14,
      'category' => 'Obstruction/Clutter',
      'location_name' => '1/K-43, Mahaveer Nagar Extension, Mahaveer Nagar, Kota, Rajasthan 324009, India',
      'latitude' => 25.1387117,
      'longitude' => 75.8322506,
      'severity' => 'Moderate Risk',
      'status' => 'Pending',
      'description' => 'An unidentified civic safety hazard, specifically an obstruction or clutter, has been reported at 1/K-43, Mahaveer Nagar Extension, Mahaveer Nagar, Kota, Rajasthan. The nature of the hazard requires further investigation by civic authorities.',
      'verification_count' => 0,
      'false_report_count' => 0,
      'resolution_votes' => 0,
      'is_archived' => 0,
      'confidence_score' => NULL,
      'ai_severity_score' => NULL,
      'ai_analysis_summary' => 'Reason: An unidentified civic safety hazard, specifically an obstruction or clutter, has been reported at 1/K-43, Mahaveer Nagar Extension, Mahaveer Nagar, Kota, Rajasthan. The nature of the hazard requires further investigation by civic authorities.

Petition:
To,
The Municipal Commissioner,
Kota Municipal Corporation,
Kota, Rajasthan.

Subject: Urgent attention required for an unidentified civic safety hazard at Mahaveer Nagar Extension.

Dear Sir/Madam,

I am writing to report an unidentified civic safety hazard located at 1/K-43, Mahaveer Nagar Extension, Mahaveer Nagar, Kota, Rajasthan 324009. The hazard, which appears to involve significant clutter and obstruction, poses a potential risk to public safety and accessibility. The coordinates for this location are 25.1387117, 75.8322506.

I urge your office to investigate this matter promptly and take the necessary actions to mitigate the risk and ensure the safety of the residents in the area. Thank you for your immediate attention to this important civic concern.

Sincerely,
Jaykishan Rawat',
      'image_path' => 'http://10.69.249.93:8000/storage/hazards/Me6lxVoIJfOpFzwNqGEXTQ2eQ0jOLUzto1dOAx4p.jpg',
      'created_by' => NULL,
      'created_at' => '2026-06-26 19:51:46',
      'updated_at' => '2026-06-26 19:51:46',
    ),
    9 => 
    array (
      'id' => 15,
      'category' => 'Unidentified Civic Safety Hazard',
      'location_name' => '1/K-43, Mahaveer Nagar Extension, Mahaveer Nagar, Kota, Rajasthan 324009, India',
      'latitude' => 25.1387114,
      'longitude' => 75.8322505,
      'severity' => 'Unknown Risk',
      'status' => 'Pending',
      'description' => 'An unidentified civic safety hazard has been reported at coordinates 25.1387114, 75.8322505, specifically in the Mahaveer Nagar Extension area of Kota, Rajasthan. The nature of the hazard is not specified in the user\'s description or the provided image, which appears to be unrelated to any civic issue. Further investigation is required to determine the exact nature and severity of the reported issue.',
      'verification_count' => 0,
      'false_report_count' => 0,
      'resolution_votes' => 0,
      'is_archived' => 0,
      'confidence_score' => NULL,
      'ai_severity_score' => NULL,
      'ai_analysis_summary' => 'Reason: An unidentified civic safety hazard has been reported at coordinates 25.1387114, 75.8322505, specifically in the Mahaveer Nagar Extension area of Kota, Rajasthan. The nature of the hazard is not specified in the user\'s description or the provided image, which appears to be unrelated to any civic issue. Further investigation is required to determine the exact nature and severity of the reported issue.

Petition:
To,
The Municipal Commissioner,
Kota Municipal Corporation,
Kota, Rajasthan

Subject: Report of Unidentified Civic Safety Hazard at 1/K-43, Mahaveer Nagar Extension

Dear Sir/Madam,

I am writing to bring to your urgent attention an unidentified civic safety hazard reported in the area of 1/K-43, Mahaveer Nagar Extension, Mahaveer Nagar, Kota, Rajasthan 324009. The exact nature of this hazard is currently unknown, but it has been identified as a potential risk to public safety.

I kindly request that your office dispatch a team to investigate this matter thoroughly at the aforementioned location (coordinates: 25.1387114, 75.8322505) and take appropriate measures to identify and mitigate any existing safety concerns.

Your prompt attention to this matter would be greatly appreciated in ensuring the safety and well-being of the residents in Mahaveer Nagar Extension.

Sincerely,
Jaykishan Rawat',
      'image_path' => 'http://10.69.249.93:8000/storage/hazards/2btmpaaQ16PmLlK25by2d8eu1QcD4SBHs6cc8If5.jpg',
      'created_by' => NULL,
      'created_at' => '2026-06-26 21:06:53',
      'updated_at' => '2026-06-26 21:06:53',
    ),
    10 => 
    array (
      'id' => 16,
      'category' => 'Waste Management / Sanitation',
      'location_name' => '1/K-43, Mahaveer Nagar Extension, Mahaveer Nagar, Kota, Rajasthan 324009, India',
      'latitude' => 25.1387122,
      'longitude' => 75.8322503,
      'severity' => 'Moderate Risk',
      'status' => 'Pending',
      'description' => 'An unidentified civic safety hazard has been reported by Jaykishan Rawat at 1/K-43, Mahaveer Nagar Extension, Mahaveer Nagar, Kota, Rajasthan. The visual evidence includes waste bins, suggesting a potential issue related to waste management or sanitation that requires investigation.',
      'verification_count' => 0,
      'false_report_count' => 0,
      'resolution_votes' => 0,
      'is_archived' => 0,
      'confidence_score' => NULL,
      'ai_severity_score' => NULL,
      'ai_analysis_summary' => 'Reason: An unidentified civic safety hazard has been reported by Jaykishan Rawat at 1/K-43, Mahaveer Nagar Extension, Mahaveer Nagar, Kota, Rajasthan. The visual evidence includes waste bins, suggesting a potential issue related to waste management or sanitation that requires investigation.

Petition:
To,
The Municipal Commissioner,
Kota Municipal Corporation,
Kota, Rajasthan.

Subject: Urgent Report of Unidentified Civic Safety Hazard at 1/K-43, Mahaveer Nagar Extension, Mahaveer Nagar, Kota.

Dear Sir/Madam,

I am writing to bring to your immediate attention an unidentified civic safety hazard observed at the location 1/K-43, Mahaveer Nagar Extension, Mahaveer Nagar, Kota, Rajasthan 324009. The exact nature of the hazard is not fully clear from the visual evidence, which includes the presence of waste bins. However, there is a concern that this situation may pose a risk to public health and safety, potentially related to improper waste management or accumulation.

I kindly request your office to dispatch a team to investigate this matter thoroughly at the earliest convenience and take necessary remedial actions to ensure the safety and well-being of the residents in the area.

Thank you for your prompt attention to this urgent civic matter.

Sincerely,
Jaykishan Rawat',
      'image_path' => 'http://10.69.249.93:8000/storage/hazards/j7MO88WFOmMmXmRcwii5EcgWKteYDqv9vOVZslBH.jpg',
      'created_by' => 32,
      'created_at' => '2026-06-26 21:15:24',
      'updated_at' => '2026-06-26 21:15:24',
    ),
    11 => 
    array (
      'id' => 17,
      'category' => 'No Hazard Identified',
      'location_name' => '1/K-43, Mahaveer Nagar Extension, Mahaveer Nagar, Kota, Rajasthan 324009, India',
      'latitude' => 25.1387112,
      'longitude' => 75.8322504,
      'severity' => 'None Risk',
      'status' => 'Pending',
      'description' => 'The reported \'unidentified civic safety hazard\' could not be confirmed based on the provided image, which depicts a domestic rabbit on a bed. No civic safety hazard is apparent from the visual evidence.',
      'verification_count' => 0,
      'false_report_count' => 0,
      'resolution_votes' => 0,
      'is_archived' => 0,
      'confidence_score' => NULL,
      'ai_severity_score' => NULL,
      'ai_analysis_summary' => 'Reason: The reported \'unidentified civic safety hazard\' could not be confirmed based on the provided image, which depicts a domestic rabbit on a bed. No civic safety hazard is apparent from the visual evidence.

Petition:
To,
The Municipal Commissioner,
Kota Municipal Corporation,
Kota, Rajasthan, India.

Subject: Clarification Regarding a Reported Unidentified Civic Safety Hazard at 1/K-43, Mahaveer Nagar Extension

Dear Sir/Madam,

This letter refers to a report of an "unidentified civic safety hazard" submitted by me, Jaykishan Rawat, concerning the location 1/K-43, Mahaveer Nagar Extension, Mahaveer Nagar, Kota, Rajasthan 324009. The coordinates provided are 25.1387112, 75.8322504.

Upon review of the accompanying details, specifically the image provided, it appears that the visual evidence depicts a domestic rabbit on a bed, which does not correspond to a civic safety hazard.

I am writing to clarify that based on the visual information, a specific civic safety hazard could not be identified. If there was an intention to report a different issue, kindly provide further details or a more relevant image.

Thank you for your attention to this matter.

Sincerely,
Jaykishan Rawat',
      'image_path' => 'https://storage.googleapis.com/nagarakshak/hazards/hazard_6a3eeea1786b1.webp',
      'created_by' => 32,
      'created_at' => '2026-06-26 21:27:27',
      'updated_at' => '2026-06-26 21:27:27',
    ),
    12 => 
    array (
      'id' => 18,
      'category' => 'Pothole',
      'location_name' => '2/F-61, Vistar Yojna, Mahaveer Nagar Extension, Mahaveer Nagar, Kota, Rajasthan 324009, India',
      'latitude' => 25.1376253,
      'longitude' => 75.8314248,
      'severity' => 'Low Risk',
      'status' => 'Pending',
      'description' => 'Manual user description.',
      'verification_count' => 0,
      'false_report_count' => 0,
      'resolution_votes' => 0,
      'is_archived' => 0,
      'confidence_score' => NULL,
      'ai_severity_score' => NULL,
      'ai_analysis_summary' => 'Reason: Manual user description.',
      'image_path' => 'https://storage.googleapis.com/nagarakshak/hazards/hazard_6a3ef33cc78da.jpg',
      'created_by' => 32,
      'created_at' => '2026-06-26 21:47:00',
      'updated_at' => '2026-06-26 21:47:00',
    ),
    13 => 
    array (
      'id' => 19,
      'category' => 'Garbage',
      'location_name' => '1/K-43, Mahaveer Nagar Extension, Mahaveer Nagar, Kota, Rajasthan 324009, India',
      'latitude' => 25.1387128,
      'longitude' => 75.8322503,
      'severity' => 'Low Risk',
      'status' => 'Pending',
      'description' => 'AI detected a potential safety hazard in public infrastructure. Action recommended.',
      'verification_count' => 0,
      'false_report_count' => 0,
      'resolution_votes' => 0,
      'is_archived' => 0,
      'confidence_score' => NULL,
      'ai_severity_score' => NULL,
      'ai_analysis_summary' => 'Reason: AI detected a potential safety hazard in public infrastructure. Action recommended.

Petition:
To,
The Municipal Commissioner,

Subject: Urgent petition regarding improper garbage disposal and waste accumulation

Respected Sir/Madam,

I am writing to formally bring to your attention the persistent issue of improper waste disposal and garbage accumulation in our locality. Scattered debris and unmanaged waste materials pose a significant public health hazard, attracting pests and creating unsanitary conditions for residents and passersby.

To prevent the deterioration of civic hygiene and safeguard public health, I earnestly request your office to direct the sanitation department to conduct regular waste clearance drives and ensure systematic garbage collection in the area.

Thanking you in anticipation of your prompt action.

Sincerely,
Concerned Resident',
      'image_path' => NULL,
      'created_by' => 32,
      'created_at' => '2026-06-27 23:26:32',
      'updated_at' => '2026-06-27 23:26:32',
    ),
    14 => 
    array (
      'id' => 20,
      'category' => 'Road Hazard',
      'location_name' => '1/K-21, Mahaveer Nagar Extension, Mahaveer Nagar, Kota, Rajasthan 324009, India',
      'latitude' => 25.1387837,
      'longitude' => 75.8321511,
      'severity' => 'Moderate',
      'status' => 'Pending',
      'description' => 'A significant pothole, filled with water, has been identified on a road in Kota at coordinates 25.1387837, 75.8321511. This poses a civic safety hazard, potentially causing damage to vehicles and increasing the risk of accidents for drivers and pedestrians due to its size and obscured depth.',
      'verification_count' => 0,
      'false_report_count' => 0,
      'resolution_votes' => 0,
      'is_archived' => 0,
      'confidence_score' => NULL,
      'ai_severity_score' => NULL,
      'ai_analysis_summary' => 'Reason: A significant pothole, filled with water, has been identified on a road in Kota at coordinates 25.1387837, 75.8321511. This poses a civic safety hazard, potentially causing damage to vehicles and increasing the risk of accidents for drivers and pedestrians due to its size and obscured depth.

Petition:
To,
The Municipal Commissioner,
Kota Municipal Corporation,
Kota.

Subject: Urgent attention required for a civic safety hazard (pothole) at 25.1387837, 75.8321511 in Kota.

Dear Sir/Madam,

I am writing to report a serious civic safety hazard in our city. A large and deep pothole, currently filled with water, is present on the road at the approximate coordinates 25.1387837, 75.8321511. This location falls within the jurisdiction of Kota.

This pothole poses a significant risk to public safety. It can cause severe damage to vehicles, including tires and suspension systems, and is a major accident hazard, especially for two-wheelers and at night when its depth is obscured by water. Pedestrians are also at risk of tripping and falling.

I urge your office to take immediate action to repair this dangerous road defect to ensure the safety of commuters and residents. Prompt intervention will prevent potential accidents and further inconvenience.

Thank you for your time and consideration.

Sincerely,
Jaykishan Rawat',
      'image_path' => 'https://storage.googleapis.com/nagarakshak/hazards/hazard_6a405e3083ea8.jpg',
      'created_by' => 32,
      'created_at' => '2026-06-27 23:35:46',
      'updated_at' => '2026-06-27 23:35:46',
    ),
    15 => 
    array (
      'id' => 21,
      'category' => 'null',
      'location_name' => '1/K-45, Vistar Yojna, Mahaveer Nagar Extension, Mahaveer Nagar, Kota, Rajasthan 324009, India',
      'latitude' => 25.13864966304,
      'longitude' => 75.832240321617,
      'severity' => 'null',
      'status' => 'Rejected',
      'description' => 'null',
      'verification_count' => 0,
      'false_report_count' => 1,
      'resolution_votes' => 0,
      'is_archived' => 0,
      'confidence_score' => NULL,
      'ai_severity_score' => NULL,
      'ai_analysis_summary' => 'Reason: null

Petition:
null',
      'image_path' => 'https://storage.googleapis.com/nagarakshak/hazards/hazard_6a41b51b53b77.jpg',
      'created_by' => 32,
      'created_at' => '2026-06-28 23:58:20',
      'updated_at' => '2026-06-28 23:58:53',
    ),
    16 => 
    array (
      'id' => 22,
      'category' => 'null',
      'location_name' => '1/K-44, Mahaveer Nagar Extension, Mahaveer Nagar, Kota, Rajasthan 324009, India',
      'latitude' => 25.138661535771,
      'longitude' => 75.832271181541,
      'severity' => 'null',
      'status' => 'Pending',
      'description' => 'null',
      'verification_count' => 0,
      'false_report_count' => 0,
      'resolution_votes' => 0,
      'is_archived' => 0,
      'confidence_score' => NULL,
      'ai_severity_score' => NULL,
      'ai_analysis_summary' => 'Reason: null

Petition:
null',
      'image_path' => 'https://storage.googleapis.com/nagarakshak/hazards/hazard_6a41b7fd5f276.jpg',
      'created_by' => 32,
      'created_at' => '2026-06-29 00:10:38',
      'updated_at' => '2026-06-29 00:10:38',
    ),
    17 => 
    array (
      'id' => 23,
      'category' => 'Open Manhole',
      'location_name' => '1/K-45, Vistar Yojna, Mahaveer Nagar Extension, Mahaveer Nagar, Kota, Rajasthan 324009, India',
      'latitude' => 25.138651958271,
      'longitude' => 75.832279340251,
      'severity' => 'High Risk',
      'status' => 'Verified',
      'description' => 'djfufig',
      'verification_count' => 1,
      'false_report_count' => 0,
      'resolution_votes' => 0,
      'is_archived' => 0,
      'confidence_score' => NULL,
      'ai_severity_score' => NULL,
      'ai_analysis_summary' => 'Reason: djfufig


Petition:
null',
      'image_path' => 'https://storage.googleapis.com/nagarakshak/hazards/hazard_6a41b8662a897.jpg',
      'created_by' => 32,
      'created_at' => '2026-06-29 00:12:23',
      'updated_at' => '2026-06-29 00:49:24',
    ),
    18 => 
    array (
      'id' => 24,
      'category' => 'Pothole',
      'location_name' => '1/K-45, Vistar Yojna, Mahaveer Nagar Extension, Mahaveer Nagar, Kota, Rajasthan 324009, India',
      'latitude' => 25.138571912913,
      'longitude' => 75.832285584291,
      'severity' => 'Medium Risk',
      'status' => 'Verified',
      'description' => 'this is a test description',
      'verification_count' => 1,
      'false_report_count' => 0,
      'resolution_votes' => 0,
      'is_archived' => 0,
      'confidence_score' => NULL,
      'ai_severity_score' => NULL,
      'ai_analysis_summary' => 'Reason: this is a test description

Petition:
null',
      'image_path' => 'https://storage.googleapis.com/nagarakshak/hazards/hazard_6a41bb0b69460.jpg,https://storage.googleapis.com/nagarakshak/hazards/hazard_6a41bb0ca81d8.jpg,https://storage.googleapis.com/nagarakshak/hazards/hazard_6a41bb0d98b4d.jpg',
      'created_by' => 32,
      'created_at' => '2026-06-29 00:23:42',
      'updated_at' => '2026-06-29 00:24:13',
    ),
    19 => 
    array (
      'id' => 25,
      'category' => 'Broken Streetlight',
      'location_name' => '32, near Lux Gas Agency, Income Tax Colony, Getor, Jagatpura, Jaipur, Rajasthan 302017, India',
      'latitude' => 26.860905192019,
      'longitude' => 75.83855896673,
      'severity' => 'High Risk',
      'status' => 'Pending',
      'description' => 'this is a high alert',
      'verification_count' => 0,
      'false_report_count' => 0,
      'resolution_votes' => 0,
      'is_archived' => 0,
      'confidence_score' => NULL,
      'ai_severity_score' => NULL,
      'ai_analysis_summary' => 'Reason: this is a high alert

Petition:
null',
      'image_path' => 'https://storage.googleapis.com/nagarakshak/hazards/hazard_6a41d74a07e2b.jpg',
      'created_by' => 32,
      'created_at' => '2026-06-29 02:24:12',
      'updated_at' => '2026-06-29 02:24:12',
    ),
    20 => 
    array (
      'id' => 26,
      'category' => 'Waterlogging',
      'location_name' => '1/K-44, Mahaveer Nagar Extension, Mahaveer Nagar, Kota, Rajasthan 324009, India',
      'latitude' => 25.1386719,
      'longitude' => 75.8322457,
      'severity' => 'High Risk',
      'status' => 'Pending',
      'description' => 'this is a test report',
      'verification_count' => 0,
      'false_report_count' => 0,
      'resolution_votes' => 0,
      'is_archived' => 0,
      'confidence_score' => NULL,
      'ai_severity_score' => NULL,
      'ai_analysis_summary' => 'Reason: this is a test report

Petition:
null',
      'image_path' => 'https://storage.googleapis.com/nagarakshak/hazards/hazard_6a41d89a8f337.jpg',
      'created_by' => 32,
      'created_at' => '2026-06-29 02:29:48',
      'updated_at' => '2026-06-29 02:29:48',
    ),
    21 => 
    array (
      'id' => 31,
      'category' => 'Pothole',
      'location_name' => '5RGJ+5J3, Garh Palace Kota Rd, Garh Palace, Kota, Rajasthan 324006, India',
      'latitude' => 25.175434100448,
      'longitude' => 75.831553332339,
      'severity' => 'High Risk',
      'status' => 'Pending',
      'description' => 'sjsbd',
      'verification_count' => 0,
      'false_report_count' => 0,
      'resolution_votes' => 0,
      'is_archived' => 0,
      'confidence_score' => NULL,
      'ai_severity_score' => NULL,
      'ai_analysis_summary' => 'Reason: sjsbd

Petition:
null',
      'image_path' => 'https://storage.googleapis.com/nagarakshak/hazards/hazard_6a41e48fc4efd.jpg',
      'created_by' => 32,
      'created_at' => '2026-06-29 03:20:48',
      'updated_at' => '2026-06-29 03:20:48',
    ),
  ),
  'notifications' => 
  array (
    0 => 
    array (
      'id' => 5,
      'title' => 'e.g. ⚠️ High Alert: Open Drain Detected',
      'body' => 'e.g. ⚠️ High Alert: Open Drain Detectede.g. ⚠️ High Alert: Open Drain Detectede.g. ⚠️ High Alert: Open Drain Detectede.g. ⚠️ High Alert: Open Drain Detectede.g. ⚠️ High Alert: Open Drain Detectede.g. ⚠️ High Alert: Open Drain Detectede.g. ⚠️ High Alert: Open Drain Detected',
      'type' => 'Emergency Alert',
      'target_type' => 'All Users',
      'sent_count' => 6,
      'delivered_count' => 6,
      'creator_id' => 27,
      'created_at' => '2026-06-28 12:24:42',
      'updated_at' => '2026-06-28 12:24:42',
    ),
    1 => 
    array (
      'id' => 6,
      'title' => '⚠️ Nearby Alert: Broken Streetlight',
      'body' => 'New Broken Streetlight reported within 500m radius at 32, near Lux Gas Agency, Income Tax Colony, Getor, Jagatpura, Jaipur, Rajasthan 302017, India. Proceed with caution!',
      'type' => 'Hazard Alert',
      'target_type' => 'Radius Based (500m)',
      'sent_count' => 1,
      'delivered_count' => 1,
      'creator_id' => 32,
      'created_at' => '2026-06-29 02:24:12',
      'updated_at' => '2026-06-29 02:24:12',
    ),
    2 => 
    array (
      'id' => 7,
      'title' => 'hh',
      'body' => 'hfhfh',
      'type' => 'Emergency Alert',
      'target_type' => 'All Users',
      'sent_count' => 7,
      'delivered_count' => 7,
      'creator_id' => 27,
      'created_at' => '2026-06-29 02:50:03',
      'updated_at' => '2026-06-29 02:50:03',
    ),
    3 => 
    array (
      'id' => 11,
      'title' => 'Test Guest Alert',
      'body' => 'Test body',
      'type' => 'Hazard Alert',
      'target_type' => 'Radius Based (500m)',
      'sent_count' => 7,
      'delivered_count' => 7,
      'creator_id' => NULL,
      'created_at' => '2026-06-29 03:15:13',
      'updated_at' => '2026-06-29 03:15:13',
    ),
    4 => 
    array (
      'id' => 12,
      'title' => 'Test Auth Alert',
      'body' => 'Test body',
      'type' => 'Hazard Alert',
      'target_type' => 'Radius Based (500m)',
      'sent_count' => 7,
      'delivered_count' => 7,
      'creator_id' => 28,
      'created_at' => '2026-06-29 03:15:14',
      'updated_at' => '2026-06-29 03:15:14',
    ),
    5 => 
    array (
      'id' => 13,
      'title' => '⚠️ Nearby Alert: Pothole',
      'body' => 'New Pothole reported within 50000m radius at MBS Road, Kota. Proceed with caution!',
      'type' => 'Hazard Alert',
      'target_type' => 'Radius Based (50000m)',
      'sent_count' => 7,
      'delivered_count' => 7,
      'creator_id' => 28,
      'created_at' => '2026-06-29 03:15:34',
      'updated_at' => '2026-06-29 03:15:34',
    ),
    6 => 
    array (
      'id' => 14,
      'title' => '⚠️ Nearby Alert: Pothole',
      'body' => 'New Pothole reported within 50000m radius at 5RGJ+5J3, Garh Palace Kota Rd, Garh Palace, Kota, Rajasthan 324006, India. Proceed with caution!',
      'type' => 'Hazard Alert',
      'target_type' => 'Radius Based (50000m)',
      'sent_count' => 7,
      'delivered_count' => 7,
      'creator_id' => 32,
      'created_at' => '2026-06-29 03:20:49',
      'updated_at' => '2026-06-29 03:20:49',
    ),
  ),
  'ai_logs' => 
  array (
    0 => 
    array (
      'id' => 4,
      'hazard_id' => 6,
      'category' => 'Pothole',
      'confidence' => 0.94,
      'response_time' => 230,
      'status' => 'Success',
      'error_message' => NULL,
      'created_at' => '2026-06-26 19:19:42',
      'updated_at' => '2026-06-26 19:19:42',
    ),
    1 => 
    array (
      'id' => 5,
      'hazard_id' => 7,
      'category' => 'Open Drain',
      'confidence' => 0.98,
      'response_time' => 310,
      'status' => 'Success',
      'error_message' => NULL,
      'created_at' => '2026-06-26 19:19:42',
      'updated_at' => '2026-06-26 19:19:42',
    ),
    2 => 
    array (
      'id' => 6,
      'hazard_id' => 8,
      'category' => 'Waterlogging',
      'confidence' => 0.89,
      'response_time' => 195,
      'status' => 'Success',
      'error_message' => NULL,
      'created_at' => '2026-06-26 19:19:42',
      'updated_at' => '2026-06-26 19:19:42',
    ),
    3 => 
    array (
      'id' => 7,
      'hazard_id' => NULL,
      'category' => 'Pothole',
      'confidence' => 0.99,
      'response_time' => 3680,
      'status' => 'Success',
      'error_message' => NULL,
      'created_at' => '2026-06-26 19:22:58',
      'updated_at' => '2026-06-26 19:22:58',
    ),
    4 => 
    array (
      'id' => 8,
      'hazard_id' => NULL,
      'category' => NULL,
      'confidence' => NULL,
      'response_time' => 12377,
      'status' => 'Failed',
      'error_message' => 'Gemini API call failed or response malformed: {
  "candidates": [
    {
      "content": {
        "parts": [
          {
            "text": "{\\n  \\"predicted_category\\": \\"Sanitation Issue\\",\\n  \\"predicted_severity\\": \\"Moderate\\",\\n  \\"confidence_score\\": 0.8,\\n  \\"generated_summary\\": \\"A civic safety hazard has been reported in Mahaveer Nagar Extension, Kota, Rajasthan. The issue appears to be related to general untidiness and scattered waste, contributing to unhygienic conditions and potential health concerns. The citizen, Jaykishan Rawat, has requested that this matter be addressed by the Municipal Commissioner.\\",\\n  \\"petition_draft\\": \\"To,\\\\nThe Municipal Commissioner,\\\\nKota Municipal Corporation,\\\\nKota, Rajasthan.\\\\n\\\\nSubject: Urgent attention required for a Civic Safety and Sanitation Hazard in Mahaveer Nagar Extension.\\\\n\\\\nRespected Sir/Madam,\\\\n\\\\nI am writing to bring to your immediate attention a significant civic safety and sanitation hazard observed at the location 1/K-43, Mahaveer Nagar Extension, Mahaveer Nagar, Kota, Rajasthan 324009. The area is experiencing issues with scattered waste and general untidiness, which contributes to unhygienic conditions and poses a potential health risk to residents.\\\\n\\\\nWhile the exact nature of the hazard is currently unidentified, the presence of accumulated litter and lack of cleanliness is a matter of concern for public well-being and environmental hygiene in our locality. We kindly request your intervention to investigate this matter thoroughly and implement necessary measures to clean up the area"
          }
        ],
        "role": "model"
      },
      "finishReason": "MAX_TOKENS",
      "index": 0
    }
  ],
  "usageMetadata": {
    "promptTokenCount": 438,
    "candidatesTokenCount": 307,
    "totalTokenCount": 2472,
    "promptTokensDetails": [
      {
        "modality": "TEXT",
        "tokenCount": 180
      },
      {
        "modality": "IMAGE",
        "tokenCount": 258
      }
    ],
    "thoughtsTokenCount": 1727,
    "serviceTier": "standard"
  },
  "modelVersion": "gemini-2.5-flash",
  "responseId": "YNI-aq2wLoDTjuMP0tPpwQ0"
}
',
      'created_at' => '2026-06-26 19:26:38',
      'updated_at' => '2026-06-26 19:26:38',
    ),
    5 => 
    array (
      'id' => 9,
      'hazard_id' => NULL,
      'category' => 'Sanitation Hazard',
      'confidence' => 0.85,
      'response_time' => 12943,
      'status' => 'Success',
      'error_message' => NULL,
      'created_at' => '2026-06-26 19:28:19',
      'updated_at' => '2026-06-26 19:28:19',
    ),
    6 => 
    array (
      'id' => 10,
      'hazard_id' => NULL,
      'category' => 'Obstruction/Clutter',
      'confidence' => 0.7,
      'response_time' => 13548,
      'status' => 'Success',
      'error_message' => NULL,
      'created_at' => '2026-06-26 19:51:32',
      'updated_at' => '2026-06-26 19:51:32',
    ),
    7 => 
    array (
      'id' => 11,
      'hazard_id' => NULL,
      'category' => 'Unidentified Civic Safety Hazard',
      'confidence' => 0.35,
      'response_time' => 7634,
      'status' => 'Success',
      'error_message' => NULL,
      'created_at' => '2026-06-26 21:06:37',
      'updated_at' => '2026-06-26 21:06:37',
    ),
    8 => 
    array (
      'id' => 12,
      'hazard_id' => NULL,
      'category' => 'Waste Management / Sanitation',
      'confidence' => 0.75,
      'response_time' => 8792,
      'status' => 'Success',
      'error_message' => NULL,
      'created_at' => '2026-06-26 21:14:56',
      'updated_at' => '2026-06-26 21:14:56',
    ),
    9 => 
    array (
      'id' => 13,
      'hazard_id' => NULL,
      'category' => 'No Hazard Identified',
      'confidence' => 0.98,
      'response_time' => 11046,
      'status' => 'Success',
      'error_message' => NULL,
      'created_at' => '2026-06-26 21:27:09',
      'updated_at' => '2026-06-26 21:27:09',
    ),
    10 => 
    array (
      'id' => 14,
      'hazard_id' => NULL,
      'category' => 'Road Hazard',
      'confidence' => 0.95,
      'response_time' => 10795,
      'status' => 'Success',
      'error_message' => NULL,
      'created_at' => '2026-06-27 23:35:26',
      'updated_at' => '2026-06-27 23:35:26',
    ),
  ),
  'comments' => 
  array (
    0 => 
    array (
      'id' => 1,
      'hazard_id' => 24,
      'user_id' => NULL,
      'user_name' => 'Municipal Corporation',
      'content' => 'Complaint registered. Assigned to PWD Division. Expected repair within 7 working days.',
      'is_official' => 1,
      'created_at' => '2026-06-29 00:43:13',
      'updated_at' => '2026-06-29 00:43:13',
    ),
    1 => 
    array (
      'id' => 2,
      'hazard_id' => 24,
      'user_id' => NULL,
      'user_name' => 'Ramesh Kumar',
      'content' => 'I passed by this area today morning, vehicle traffic was slow due to this.',
      'is_official' => 0,
      'created_at' => '2026-06-29 00:43:13',
      'updated_at' => '2026-06-29 00:43:13',
    ),
    2 => 
    array (
      'id' => 3,
      'hazard_id' => 24,
      'user_id' => NULL,
      'user_name' => 'Jaykishan Rawat',
      'content' => 'hi',
      'is_official' => 0,
      'created_at' => '2026-06-29 00:43:25',
      'updated_at' => '2026-06-29 00:43:25',
    ),
    3 => 
    array (
      'id' => 4,
      'hazard_id' => 23,
      'user_id' => NULL,
      'user_name' => 'Municipal Corporation',
      'content' => 'Complaint registered. Assigned to PWD Division. Expected repair within 7 working days.',
      'is_official' => 1,
      'created_at' => '2026-06-29 00:49:21',
      'updated_at' => '2026-06-29 00:49:21',
    ),
    4 => 
    array (
      'id' => 5,
      'hazard_id' => 23,
      'user_id' => NULL,
      'user_name' => 'Ramesh Kumar',
      'content' => 'I passed by this area today morning, vehicle traffic was slow due to this.',
      'is_official' => 0,
      'created_at' => '2026-06-29 00:49:21',
      'updated_at' => '2026-06-29 00:49:21',
    ),
    5 => 
    array (
      'id' => 6,
      'hazard_id' => 25,
      'user_id' => NULL,
      'user_name' => 'Municipal Corporation',
      'content' => 'Complaint registered. Assigned to PWD Division. Expected repair within 7 working days.',
      'is_official' => 1,
      'created_at' => '2026-06-29 02:24:26',
      'updated_at' => '2026-06-29 02:24:26',
    ),
    6 => 
    array (
      'id' => 7,
      'hazard_id' => 25,
      'user_id' => NULL,
      'user_name' => 'Ramesh Kumar',
      'content' => 'I passed by this area today morning, vehicle traffic was slow due to this.',
      'is_official' => 0,
      'created_at' => '2026-06-29 02:24:26',
      'updated_at' => '2026-06-29 02:24:26',
    ),
    7 => 
    array (
      'id' => 8,
      'hazard_id' => 12,
      'user_id' => NULL,
      'user_name' => 'Municipal Corporation',
      'content' => 'Complaint registered. Assigned to PWD Division. Expected repair within 7 working days.',
      'is_official' => 1,
      'created_at' => '2026-06-29 02:28:11',
      'updated_at' => '2026-06-29 02:28:11',
    ),
    8 => 
    array (
      'id' => 9,
      'hazard_id' => 12,
      'user_id' => NULL,
      'user_name' => 'Ramesh Kumar',
      'content' => 'I passed by this area today morning, vehicle traffic was slow due to this.',
      'is_official' => 0,
      'created_at' => '2026-06-29 02:28:11',
      'updated_at' => '2026-06-29 02:28:11',
    ),
    9 => 
    array (
      'id' => 10,
      'hazard_id' => 26,
      'user_id' => NULL,
      'user_name' => 'Municipal Corporation',
      'content' => 'Complaint registered. Assigned to PWD Division. Expected repair within 7 working days.',
      'is_official' => 1,
      'created_at' => '2026-06-29 03:10:55',
      'updated_at' => '2026-06-29 03:10:55',
    ),
    10 => 
    array (
      'id' => 11,
      'hazard_id' => 26,
      'user_id' => NULL,
      'user_name' => 'Ramesh Kumar',
      'content' => 'I passed by this area today morning, vehicle traffic was slow due to this.',
      'is_official' => 0,
      'created_at' => '2026-06-29 03:10:55',
      'updated_at' => '2026-06-29 03:10:55',
    ),
    11 => 
    array (
      'id' => 12,
      'hazard_id' => 6,
      'user_id' => NULL,
      'user_name' => 'Municipal Corporation',
      'content' => 'Complaint registered. Assigned to PWD Division. Expected repair within 7 working days.',
      'is_official' => 1,
      'created_at' => '2026-06-29 03:42:16',
      'updated_at' => '2026-06-29 03:42:16',
    ),
    12 => 
    array (
      'id' => 13,
      'hazard_id' => 6,
      'user_id' => NULL,
      'user_name' => 'Ramesh Kumar',
      'content' => 'I passed by this area today morning, vehicle traffic was slow due to this.',
      'is_official' => 0,
      'created_at' => '2026-06-29 03:42:16',
      'updated_at' => '2026-06-29 03:42:16',
    ),
    13 => 
    array (
      'id' => 14,
      'hazard_id' => 16,
      'user_id' => NULL,
      'user_name' => 'Municipal Corporation',
      'content' => 'Complaint registered. Assigned to PWD Division. Expected repair within 7 working days.',
      'is_official' => 1,
      'created_at' => '2026-06-29 03:42:40',
      'updated_at' => '2026-06-29 03:42:40',
    ),
    14 => 
    array (
      'id' => 15,
      'hazard_id' => 16,
      'user_id' => NULL,
      'user_name' => 'Ramesh Kumar',
      'content' => 'I passed by this area today morning, vehicle traffic was slow due to this.',
      'is_official' => 0,
      'created_at' => '2026-06-29 03:42:40',
      'updated_at' => '2026-06-29 03:42:40',
    ),
    15 => 
    array (
      'id' => 16,
      'hazard_id' => 22,
      'user_id' => NULL,
      'user_name' => 'Municipal Corporation',
      'content' => 'Complaint registered. Assigned to PWD Division. Expected repair within 7 working days.',
      'is_official' => 1,
      'created_at' => '2026-06-29 03:42:46',
      'updated_at' => '2026-06-29 03:42:46',
    ),
    16 => 
    array (
      'id' => 17,
      'hazard_id' => 22,
      'user_id' => NULL,
      'user_name' => 'Ramesh Kumar',
      'content' => 'I passed by this area today morning, vehicle traffic was slow due to this.',
      'is_official' => 0,
      'created_at' => '2026-06-29 03:42:46',
      'updated_at' => '2026-06-29 03:42:46',
    ),
  ),
  'verifications' => 
  array (
    0 => 
    array (
      'id' => 3,
      'hazard_id' => 6,
      'user_id' => 29,
      'status' => 'Verified',
      'evidence_path' => NULL,
      'notes' => 'Confirmed. Almost fell while riding a scooter here.',
      'created_at' => '2026-06-26 19:19:42',
      'updated_at' => '2026-06-26 19:19:42',
    ),
    1 => 
    array (
      'id' => 4,
      'hazard_id' => 7,
      'user_id' => 30,
      'status' => 'Verified',
      'evidence_path' => NULL,
      'notes' => 'Still uncovered as of this morning. Very dangerous.',
      'created_at' => '2026-06-26 19:19:42',
      'updated_at' => '2026-06-26 19:19:42',
    ),
  ),
);

        foreach ($data as $table => $rows) {
            if (empty($rows)) {
                continue;
            }

            if (Schema::hasTable($table)) {
                // Truncate existing data to avoid primary key collisions
                DB::table($table)->truncate();

                $columns = Schema::getColumnListing($table);
                $filteredRows = array_map(function ($row) use ($columns) {
                    return array_intersect_key($row, array_flip($columns));
                }, $rows);

                foreach (array_chunk($filteredRows, 500) as $chunk) {
                    DB::table($table)->insert($chunk);
                }
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}