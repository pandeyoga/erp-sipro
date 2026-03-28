<?php

/**
 * Permissions Configuration Structure.
 *
 * This configuration defines access control grouped by module and feature.
 * Each permission item is formatted as: {feature}.{permission_item}
 *
 * Format:
 * [
 *     'module_key' => [                          // string: Name of the module
 *         'features' => [
 *             'feature_key' => [                 // string: Name of the feature
 *                 'permission_item_1',           // string: Permission action (e.g. 'create')
 *                 'permission_item_2',           // string: Additional actions (e.g. 'update', 'delete')
 *                 ...
 *             ],
 *         ],
 *     ],
 *     ...
 * ]
 *
 * Example:
 * [
 *     'crm' => [
 *         'features' => [
 *             'leads_management' => [
 *                 'create',                     // becomes: 'leads_management.create'
 *                 'update_status',              // becomes: 'leads_management.update_status'
 *                 'upload_file',                // becomes: 'leads_management.upload_file'
 *                 'delete_lead',                // becomes: 'leads_management.delete_lead'
 *             ],
 *         ],
 *     ],
 * ]
 * 
 * Apart from the available permission codes, there is a code that can access everything, namely the code "all_access"
 *
 * @return array<string, array{features: array<string, string[]>}>
 */

return [
    "base" => [
        "features" => [
            "role" => [
                [
                    "label" => "Get all Permission Items",
                    "code" => "role.get_all_permission_items",
                ],
                [
                    "label" => "Get all Roles",
                    "code" => "role.get_all",
                ],
                [
                    "label" => "Create Role",
                    "code" => "role.create",
                ],
                [
                    "label" => "Get Role",
                    "code" => "role.show",
                ],
                [
                    "label" => "Update Role",
                    "code" => "role.update",
                ],
                [
                    "label" => "Delete Role",
                    "code" => "role.delete",
                ],
            ],
            "user" => [
                [
                    "label" => "Get all Users",
                    "code" => "user.get_all",
                ],
                [
                    "label" => "Create User",
                    "code" => "user.create",
                ],
                [
                    "label" => "Get User",
                    "code" => "user.show",
                ],
                [
                    "label" => "Update User",
                    "code" => "user.update",
                ],
                [
                    "label" => "Delete User",
                    "code" => "user.delete",
                ],
            ],
            "lead" => [
                [
                    "label" => "Create Lead",
                    "code" => "lead.create",
                ],
                [
                    "label" => "Update Lead Status",
                    "code" => "lead.update_status",
                ],
                [
                    "label" => "Upload File",
                    "code" => "lead.upload_file",
                ],
                [
                    "label" => "Delete Lead",
                    "code" => "lead.delete_lead",
                ],
            ],
        ]
    ],
];
