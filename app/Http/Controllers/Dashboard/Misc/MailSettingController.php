<?php

namespace App\Http\Controllers\Dashboard\Misc;

use App\Http\Controllers\_core\DashboardController;
use App\Http\Requests\Setting\MailSettingUpdateRequest;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MailSettingController extends DashboardController
{
    public function update(MailSettingUpdateRequest $request)
    {
        $validate = $request->validated();
        try {
            DB::beginTransaction();
            foreach ($validate as $key => $value) {
                $setting = Setting::where('key', $key)->firstOrFail();
                $setting->update(['value' => $value]);
            }
            DB::commit();

            return redirect()->back()->with('success', 'Database settings updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            throw new ValidationException('Error updating database settings', $e->getMessage());
        }
    }
}
