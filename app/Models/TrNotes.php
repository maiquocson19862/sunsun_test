<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class TrNotes extends Model
{
    protected $fillable;

    // Table Name
    protected $table = 'tr_notes';

    protected $primaryKey = 'tr_notes_id';
    // Timestamps
    public $timestamps = false;
    public function __construct(array $attributes = [])
    {
        $this->fillable = [
            config('const.db.tr_notes.DATE_NOTES'),
            config('const.db.tr_notes.TXT_NOTES')
        ];
        parent::__construct($attributes);
    }
    public function save_notes($request)
    {
        $status = true;
        try {
            $rowNotes = TrNotes::where('date_notes','=', $request->date_notes)->first();
            if ($rowNotes === null) {
                $rowNotes = new TrNotes;
                $rowNotes->date_notes = $request->date_notes;
            }
            $rowNotes->txt_notes = $request->txt_notes;
            $rowNotes->save();
        } catch (Exception $ex) {
            $status = false;
            Log::debug($ex->getMessage());
        }
        return [
            'status' => $status
        ];
    }
}
