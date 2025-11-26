<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get department IDs for foreign key references
        $departments = DB::table('departments')->get()->keyBy('code');

        $positions = [
            // Flight Operations Positions (Crew)
            [
                'title' => 'Captain',
                'code' => 'CAPT',
                'description' => 'Aircraft commander responsible for flight operations',
                'department_id' => $departments['OPS']->id,
                'type' => 'crew',
                'required_qualifications' => json_encode(['Commercial Pilot License', 'ATPL', 'Type Rating', 'Medical Certificate']),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'First Officer',
                'code' => 'FO',
                'description' => 'Co-pilot assisting the captain',
                'department_id' => $departments['OPS']->id,
                'type' => 'crew',
                'required_qualifications' => json_encode(['Commercial Pilot License', 'Type Rating', 'Medical Certificate']),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Flight Engineer',
                'code' => 'FE',
                'description' => 'Responsible for aircraft systems monitoring',
                'department_id' => $departments['OPS']->id,
                'type' => 'crew',
                'required_qualifications' => json_encode(['Flight Engineer License', 'Aircraft Systems Certificate']),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Cabin Crew',
                'code' => 'CC',
                'description' => 'Flight attendant providing passenger services',
                'department_id' => $departments['OPS']->id,
                'type' => 'crew',
                'required_qualifications' => json_encode(['Cabin Crew License', 'Safety Training Certificate']),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Flight Operations Staff
            [
                'title' => 'Flight Operations Manager',
                'code' => 'OPS_MGR',
                'description' => 'Manages flight operations department',
                'department_id' => $departments['OPS']->id,
                'type' => 'staff',
                'required_qualifications' => json_encode(['Management Experience', 'Aviation Operations Background']),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Crew Scheduler',
                'code' => 'CREW_SCHED',
                'description' => 'Responsible for crew scheduling and roster management',
                'department_id' => $departments['OPS']->id,
                'type' => 'staff',
                'required_qualifications' => json_encode(['Scheduling Software Knowledge', 'Aviation Regulations Knowledge']),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Flight Dispatcher',
                'code' => 'DISPATCH',
                'description' => 'Flight planning and dispatch operations',
                'department_id' => $departments['OPS']->id,
                'type' => 'staff',
                'required_qualifications' => json_encode(['Dispatcher License', 'Weather Training', 'Flight Planning']),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Aircraft Maintenance Positions
            [
                'title' => 'Maintenance Manager',
                'code' => 'MAINT_MGR',
                'description' => 'Manages aircraft maintenance department',
                'department_id' => $departments['MAINT']->id,
                'type' => 'staff',
                'required_qualifications' => json_encode(['Aircraft Maintenance License', 'Management Experience']),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Licensed Aircraft Mechanic',
                'code' => 'AME',
                'description' => 'Licensed mechanic for aircraft maintenance',
                'department_id' => $departments['MAINT']->id,
                'type' => 'staff',
                'required_qualifications' => json_encode(['Aircraft Maintenance Engineer License', 'Type Certification']),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Avionics Technician',
                'code' => 'AVIONICS',
                'description' => 'Specialist in aircraft electronic systems',
                'department_id' => $departments['MAINT']->id,
                'type' => 'staff',
                'required_qualifications' => json_encode(['Avionics Certification', 'Electronics Training']),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Finance & Accounting Positions
            [
                'title' => 'Finance Manager',
                'code' => 'FIN_MGR',
                'description' => 'Manages financial operations and accounting',
                'department_id' => $departments['FIN']->id,
                'type' => 'staff',
                'required_qualifications' => json_encode(['Accounting Degree', 'CPA', 'Management Experience']),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Accountant',
                'code' => 'ACCOUNTANT',
                'description' => 'Handles daily accounting operations',
                'department_id' => $departments['FIN']->id,
                'type' => 'staff',
                'required_qualifications' => json_encode(['Accounting Degree', 'Bookkeeping Experience']),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Ground Services Positions
            [
                'title' => 'Ground Services Manager',
                'code' => 'GND_MGR',
                'description' => 'Manages ground handling operations',
                'department_id' => $departments['GND']->id,
                'type' => 'staff',
                'required_qualifications' => json_encode(['Ground Operations Experience', 'Management Certification']),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Ground Handling Agent',
                'code' => 'GHA',
                'description' => 'Ground handling and passenger services',
                'department_id' => $departments['GND']->id,
                'type' => 'staff',
                'required_qualifications' => json_encode(['Ground Handling Training', 'Customer Service']),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Management Positions
            [
                'title' => 'Chief Executive Officer',
                'code' => 'CEO',
                'description' => 'Chief executive officer',
                'department_id' => $departments['MGMT']->id,
                'type' => 'staff',
                'required_qualifications' => json_encode(['Executive Experience', 'Aviation Industry Knowledge']),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'General Manager',
                'code' => 'GM',
                'description' => 'General manager overseeing operations',
                'department_id' => $departments['MGMT']->id,
                'type' => 'staff',
                'required_qualifications' => json_encode(['Management Experience', 'Aviation Operations']),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Safety & Security Positions
            [
                'title' => 'Safety Manager',
                'code' => 'SAFETY_MGR',
                'description' => 'Manages aviation safety programs',
                'department_id' => $departments['SAFE']->id,
                'type' => 'staff',
                'required_qualifications' => json_encode(['Aviation Safety Certification', 'SMS Training']),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Administration Positions
            [
                'title' => 'HR Manager',
                'code' => 'HR_MGR',
                'description' => 'Human resources manager',
                'department_id' => $departments['ADMIN']->id,
                'type' => 'staff',
                'required_qualifications' => json_encode(['HR Degree', 'Employment Law Knowledge']),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Administrative Assistant',
                'code' => 'ADMIN_ASST',
                'description' => 'General administrative support',
                'department_id' => $departments['ADMIN']->id,
                'type' => 'staff',
                'required_qualifications' => json_encode(['Office Administration', 'Computer Skills']),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('positions')->insert($positions);
    }
}
