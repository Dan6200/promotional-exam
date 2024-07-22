<?php

namespace App\Services;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Facades\Log;

class DocumentGenerator
{
    public function generate($record, $templatePath)
    {
        // Create new template processor for each record
        $tempDoc = new TemplateProcessor($templatePath);

        // Replace placeholders in the template
        foreach ($record as $field => $value)
        {
            try
            {
                $tempDoc->setValue($field, $value);
            }
            catch (\Exception $e)
            {
                Log::error("Error setting value for field $field: " . $e->getMessage());
                throw new \Exception("Error setting value for field $field");
            }
        }

        // Save the generated document to a temporary location
        $tempFilePath = tempnam(sys_get_temp_dir(), 'generated_doc') . '.docx';
        try
        {
            $tempDoc->saveAs($tempFilePath);
            return $tempFilePath;
        }
        catch (\Exception $e)
        {
            Log::error('Error saving document: ' . $e->getMessage());
            throw new \Exception('Error saving document: ' . $e->getMessage);
        }
    }

    private function sanitizeFilename($fileName)
    {
        return preg_replace('/[^a-zA-Z0-9]/', '_', $fileName);
    }
}
