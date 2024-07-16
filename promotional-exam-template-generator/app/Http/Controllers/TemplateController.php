<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;
use League\Csv\Statement;

class TemplateController extends Controller
{
    //
    public function index() {
        return view('upload');
    }

    public function upload(Request $request) {
        $request->validate([
            'csv' => 'required|mimes:csv,txt'
        ]);
        $path = $request->file('csv')->store('uploads');
        return redirect()->route('generate')->with('path',$path);
    }

    public function generate() {
        $path=session('path');
        if (!$path) {
            return redirect('/')->withErrors('No CSV file uploaded');
        }
        $csv = Reader::createFromPath(storage_path('app/' . path), 'r');
        $csv->setHeaderOffset(0);
        $records = (new Statement())->process($csv);
        $template = Storage::get($templatePath);

        $generatedDocuments = [];

        foreach ($records as $record) {
            $document = $template;
            foreach ($record as $field => $value) {
                $document = str_replace("<<$field>>", $value, $document);
            }
            $generatedDocuments[]=$document;
        }
        return view('generated', ['documents' => $generatedDocuments]);
    }

}
