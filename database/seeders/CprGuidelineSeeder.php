<?php

namespace Database\Seeders;

use App\Models\Guideline;
use App\Models\Team;
use Illuminate\Database\Seeder;

class CprGuidelineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $team = Team::where('name', 'Copy, Paste, Repeat')->sole();
        if (! is_null($team)) {
            Guideline::factory()->for($team)
                ->create([
                    'score' => 0.5,
                    'description' => 'Text changes, feature flag change, usually no testing required',
                ]);

            Guideline::factory()->for($team)
                ->hasTickets(['ticket_number' => 'CAE-72'])
                ->create([
                    'score' => 1,
                    'description' => 'Some testing required but not extensive, minor migration script for example',
                ]);

            Guideline::factory()->for($team)
                ->hasBullets(['body' => 'Migration that might be a little more extensive (regular complexity)'])
                ->hasTickets(['ticket_number' => 'CAE-75'])
                ->create([
                    'score' => 2,
                    'description' => 'Smaller front end change, or smaller back end change, not both sides',
                ]);

            Guideline::factory()->for($team)
                ->hasTickets(['ticket_number' => 'CAE-169'])
                ->create([
                    'score' => 3,
                    'description' => '"low 5 :)" - Could have changes on both fe & be but less complex or fairly simple on both sides',
                ]);

            Guideline::factory()->for($team)
                ->hasBullets(['body' => 'Working with legacy code / new tech, some unknown'])
                ->hasBullets(['body' => 'Backend/frontend only but more complex / extensive testing'])
                ->hasTickets(['ticket_number' => 'CAE-5'])
                ->create([
                    'score' => 5,
                    'description' => 'Front end + backend changes w/ tests on both sides',
                ]);

            Guideline::factory()->for($team)
                ->hasBullets(['body' => 'Cypress tests, rebaselining schema, new seed data'])
                ->hasBullets(['body' => 'Pipeline changes or CI/CD changes'])
                ->hasTickets(['ticket_number' => 'DEV-12920'])
                ->create([
                    'score' => 8,
                    'description' => '"high 5" - some unknowns, higher complexity',
                ]);

            Guideline::factory()->for($team)
                ->hasBullets(['body' => 'Extensive front-end and back-end changes, extensive testing, multiple components, hybrid implications'])
                ->hasBullets(['body' => 'Unknowns, new technologies'])
                ->hasTickets(['ticket_number' => 'CAE-87'])
                ->hasTickets(['ticket_number' => 'CAE-84'])
                ->create([
                    'score' => 13,
                    'description' => 'Largest recommendable size for a sprint, consider breaking down but might be acceptable based on the story',
                ]);

            Guideline::factory()->for($team)
                ->hasBullets(['body' => 'php memory leak on tests'])
                ->hasBullets(['body' => 'go-modules test timeouts on CI'])
                ->create([
                    'score' => 21,
                    'description' => 'Needs to be broken up, can\'t be broken up but will take more than a sprint to complete',
                ]);
        }
    }
}
