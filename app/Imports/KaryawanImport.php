<?php
  
namespace App\Imports;
  
use App\Models\Karyawan;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Validators\ValidationException;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
  
class KaryawanImport implements ToModel, WithValidation, WithHeadingRow, WithEvents, ToCollection
{
    use Importable, RegistersEventListeners;
    public static function beforeImport(BeforeImport $event)
    {
        $worksheet = $event->reader->getActiveSheet();
        $highestRow = $worksheet->getHighestRow(); // e.g. 10

        if ($highestRow < 2) {
            $error = \Illuminate\Validation\ValidationException::withMessages([]);
            $failure = new Failure(1, 'rows', [0 => 'Now enough rows!']);
            $failures = [0 => $failure];
            throw new ValidationException($error, $failures);
        }
    }
    
    public function collection(Collection $rows)
    {
        return $rows;
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Karyawan([
            'nama'              => $row['nama'],
            'nomor_aplikasi'    => $row['nomor_aplikasi'],
            'jabatan'           => $row['jabatan'],
            'divisi'            => $row['divisi'],
            'awal_masuk'        => $this->transformDate($row['awal_masuk']),
            'cuti'              => $row['cuti'],
        ]);
    }

    /**
     * Transform a date value into a Carbon object.
     *
     * @return \Carbon\Carbon|null
     */
    public function transformDate($value, $format = 'Y-m-d')
    {
        try {
            return \Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value));
        } catch (\ErrorException $e) {
            return \Carbon\Carbon::createFromFormat($format, $value);
        }
    }


    public function headingRow(): int
    {
        return 1;
    }

    public function rules(): array
    {
        return [
            'nama'              => 'required|max:255',
            '*.nama'            => 'required|max:255',
            'nomor_aplikasi'    => 'required|digits_between:1,255',
            '*.nomor_aplikasi'  => 'required|digits_between:1,255',
            'jabatan'           => 'required|max:255', 
            '*.jabatan'         => 'required|max:255', 
            'divisi'            => 'required|max:255', 
            '*.divisi'          => 'required|max:255', 
            'awal_masuk'        => 'required|date_format:Y-m-d',
            '*.awal_masuk'      => 'required|date_format:Y-m-d',
            'cuti'              => 'required|digits_between:1,2',
            '*.cuti'            => 'required|digits_between:1,2',
        ];
    }


}