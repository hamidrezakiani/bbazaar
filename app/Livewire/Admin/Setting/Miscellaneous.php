<?php

namespace App\Livewire\Admin\Setting;

use App\Helper\Setting;
use App\Models\Backup;
use Exception;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Number;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Livewire\Component;
use Filament\Forms\Form;
use Illuminate\Contracts\View\View;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Forms\Concerns\InteractsWithForms;
use Symfony\Component\Process\Process;
use Illuminate\Support\Collection;
use ZipArchive;

class Miscellaneous extends Component implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;

    public ?array $data = [];
    public $setting;
    protected collection $databases;

    public function mount(): void
    {
        $this->setting = \App\Models\Setting::first();
        $this->form->fill([
            'attach_pdf' => $this->setting->attach_pdf,
            'cookie_banner' => $this->setting->cookie_banner,
            'guest_checkout' => $this->setting->guest_checkout,
            'send_seller_email' => $this->setting->send_seller_email,
            'vendor_registration' => $this->setting->vendor_registration,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Toggle::make('attach_pdf')
                    ->label('Attach PDF in user email')
                    ->onIcon('tabler-check')
                    ->offIcon('tabler-x')
                    ->default(false),
                Toggle::make('cookie_banner')
                    ->onIcon('tabler-eye')
                    ->offIcon('tabler-eye-off')
                    ->label('Cookie Banner')
                    ->default(false),
                Toggle::make('guest_checkout')
                    ->onIcon('tabler-check')
                    ->offIcon('tabler-x')
                    ->label('Guest Checkout')
                    ->default(false),
                Toggle::make('send_seller_email')
                    ->onIcon('tabler-check')
                    ->offIcon('tabler-x')
                    ->label('Sent email to the seller on new orders')
                    ->default(false),
                Toggle::make('vendor_registration')
                    ->onIcon('tabler-eye')
                    ->offIcon('tabler-eye-off')
                    ->label('Vendor Registration')
                    ->default(false),
            ])->statePath('data');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Backup::query()->latest())
            ->heading('Database Backup')
            ->columns([
                TextColumn::make('filename'),
                TextColumn::make('file_type')->badge()
                    ->formatStateUsing(fn ($state) => Str::upper($state)),
                TextColumn::make('size')->formatStateUsing(fn($state) => Number::fileSize($state, precision: 2)),
                TextColumn::make('datetime')->formatStateUsing(fn($state) => Setting::dateTime($state)),
            ])
            ->actions([
                \App\Helper\Actions::CustomDelete()
                    ->before(function ($record) {
                        $filePath = '';
                        if($record->file_type === 'sql'){
                            $filePath = storage_path('app/database/' . $record->filename);
                        }else {
                            $filePath = storage_path('app/backups/' . $record->filename);
                        }

                        try {
                            if (File::exists($filePath)) {
                                File::delete($filePath);
                                $record->delete();
                                Notification::make()
                                    ->title('Database Backup Deleted...')
                                    ->success()
                                    ->inline()
                                    ->send();
                            } else {
                                throw new Exception('File Not exists.');
                            }
                        } catch (\Exception $ex) {
                            Notification::make()
                                ->title('Error')
                                ->body($ex->getMessage())
                                ->danger()
                                ->send();
                        }

                    }),
                Action::make('download')
                    ->iconButton()
                    ->button()
                    ->hiddenLabel()
                    ->action(function ($record) {
                        $filePath = '';
                        if($record->file_type === 'sql'){
                            $filePath = storage_path('app/database/' . $record->filename);
                        }else {
                            $filePath = storage_path('app/backups/' . $record->filename);
                        }
                        return response()->download($filePath);
                    })
                    ->icon('tabler-download')
            ])
            ->headerActions([
                Action::make('Backup Database')
                    ->action(fn() => $this->takeSqlDumpBackup()),
                Action::make('Backup File')
                    ->action(fn() => $this->takeDirectoryBackup())
            ]);
    }

    public function submit(): void
    {

        try {
            $this->setting->update([
                'attach_pdf' => $this->data['attach_pdf'],
                'cookie_banner' => $this->data['cookie_banner'],
                'guest_checkout' => $this->data['guest_checkout'],
                'send_seller_email' => $this->data['send_seller_email'],
                'vendor_registration' => $this->data['vendor_registration']
            ]);
            Notification::make()
                ->title('Successfully')
                ->success()
                ->body('Miscellaneous updated successfully')
                ->send();

        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function render(): View
    {
        return view('livewire.admin.setting.miscellaneous');
    }

    public function takeSqlDumpBackup(): void
    {
        try {
            $databaseName = Env::get('DB_DATABASE');
            $username = Env::get('DB_USERNAME');
            $password = Env::get('DB_PASSWORD');
            $host = Env::get('DB_HOST');
            $backupSqlName = time();
            $backupPath = storage_path('app/database/backup_' . $backupSqlName . '.sql');

            // Build the mysqldump command
            $command = sprintf(
                'mysqldump --user=%s --password=%s --host=%s --databases %s --result-file=%s',
                $username,
                $password,
                $host,
                $databaseName,
                $backupPath
            );

            // Execute the mysqldump command
            $process = Process::fromShellCommandline($command);
            $process->run();

            if ($process->isSuccessful()) {

                $filename = 'backup_' . $backupSqlName . '.sql';
                $size = File::size($backupPath);
                $datetime = now();

                // Insert the backup information into the database
                Backup::create([
                    'filename' => $filename,
                    'file_type' => 'sql',
                    'size' => $size,
                    'datetime' => $datetime,
                    'admin_id' => Auth::user()->id,
                ]);

                Notification::make()
                    ->title('Database Backup')
                    ->body('Backup completed successfully')
                    ->success()
                    ->send();
            } else {
                throw new Exception('Backup Failed.');
            }

        } catch (\Exception $ex) {
            Notification::make()
                ->title('Error')
                ->body($ex->getMessage())
                ->danger()
                ->send();
        }
    }

    public function takeDirectoryBackup():void
    {
        try {
            // Define the backup name and path
            $backupName = 'backup_' . time() . '.zip';
            $backupPath = storage_path('app/backups/' . $backupName);

            // Create a new ZipArchive instance
            $zip = new ZipArchive();

            if ($zip->open($backupPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
                // Get all files and directories in the Laravel application storage path
                $files = Storage::disk('local')->allFiles();

                // Add each file to the zip archive
                foreach ($files as $file) {
                    // Get the file contents
                    $fileContent = Storage::disk('local')->get($file);

                    // Add the file to the zip archive with its relative path
                    $zip->addFromString($file, $fileContent);
                }

                // Close the zip archive
                $zip->close();

                // Insert the backup information into the database
                $backup = new Backup();
                $backup->filename = $backupName;
                $backup->file_type = 'zip';
                $backup->size = File::size($backupPath);
                $backup->datetime = now();
                $backup->admin_id = Auth::user()->id;
                $backup->save();

                Notification::make()
                    ->title('Application Backup')
                    ->body('Backup completed successfully')
                    ->success()
                    ->send();
            } else {
                throw new Exception('Backup Failed.');
            }
        } catch (\Exception $ex) {
            Notification::make()
                ->title('Error')
                ->body($ex->getMessage())
                ->danger()
                ->send();
        }

    }
}
