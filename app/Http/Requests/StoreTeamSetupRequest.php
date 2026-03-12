<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreTeamSetupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'mode' => ['required', 'in:default,custom'],
            'custom_teams' => ['nullable', 'string'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if (count($this->teams()) < 2) {
                $validator->errors()->add('custom_teams', 'Inserisci almeno 2 squadre valide.');
            }
        });
    }

    public function teams(): array
    {
        $mode = (string) $this->validated('mode');

        if ($mode === 'default') {
            return config('teams.list', []);
        }

        return $this->parseCustomTeams((string) $this->validated('custom_teams', ''));
    }

    private function parseCustomTeams(string $rawInput): array
    {
        $chunks = preg_split('/[\r\n,;]+/', $rawInput) ?: [];
        $trimmed = array_map(static fn (string $item): string => trim($item), $chunks);
        $filtered = array_filter($trimmed, static fn (string $item): bool => $item !== '');

        return array_values(array_unique($filtered));
    }
}
