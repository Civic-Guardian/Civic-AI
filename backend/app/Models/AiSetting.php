<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider',
        'api_key',
        'model_name',
        'confidence_threshold',
        'classification_prompt',
        'auto_classification',
        'auto_severity_detection',
        'temperature',
        'max_tokens',
    ];

    protected $casts = [
        'auto_classification' => 'boolean',
        'auto_severity_detection' => 'boolean',
        'temperature' => 'float',
        'confidence_threshold' => 'float',
        'max_tokens' => 'integer',
    ];

    /**
     * Accessor for api_key.
     * Safely decrypts using default serialize/unserialize or raw string fallback.
     */
    public function getApiKeyAttribute($value)
    {
        if (empty($value)) {
            return null;
        }

        try {
            return \Illuminate\Support\Facades\Crypt::decrypt($value);
        } catch (\Throwable $e) {
            try {
                return \Illuminate\Support\Facades\Crypt::decryptString($value);
            } catch (\Throwable $e2) {
                \Illuminate\Support\Facades\Log::warning('Failed to decrypt AiSetting api_key (MAC invalid/Key mismatch): ' . $e2->getMessage());
                return null;
            }
        }
    }

    /**
     * Mutator for api_key.
     * Encrypts the key.
     */
    public function setApiKeyAttribute($value)
    {
        if (empty($value)) {
            $this->attributes['api_key'] = null;
        } else {
            $this->attributes['api_key'] = \Illuminate\Support\Facades\Crypt::encrypt($value);
        }
    }
}
