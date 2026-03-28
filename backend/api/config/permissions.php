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
            "menu" => [
                [
                    "label" => "Menu User",
                    "code" => "user.get_all",
                ],
                [
                    "label" => "Menu Role",
                    "code" => "role.get_all",
                ],
                [
                    "label" => "Menu Contact",
                    "code" => "contact.get_all",
                ],
                [
                    "label" => "Menu Lead",
                    "code" => "lead.get_all",
                ],
                [
                    "label" => "Menu Survey",
                    "code" => "survey.get_all",
                ],
                [
                    "label" => "Menu Reservation",
                    "code" => "lead.get_all_reservation",
                ],
                [
                    "label" => "Menu Document Legal",
                    "code" => "lead.get_all_documents",
                ],
                [
                    "label" => "Menu Payment",
                    "code" => "lead.get_all_payment",
                ],
                [
                    "label" => "Menu Legalitas Akhir",
                    "code" => "lead.get_all_final_legality",
                ],
                [
                    "label" => "Menu Project",
                    "code" => "property.get_all_project",
                ],
                [
                    "label" => "Menu Cluster",
                    "code" => "property.get_all_cluster",
                ],
                [
                    "label" => "Menu Unit",
                    "code" => "property.get_all_unit",
                ],
                [
                    "label" => "Menu Site Plan",
                    "code" => "property.get_all_property",
                ],
                [
                    "label" => "Menu Construction",
                    "code" => "property.get_all_construction",
                ],
                [
                    "label" => "Menu Retention Case",
                    "code" => "property.get_all_retention",
                ],
                [
                    "label" => "Menu Sub Contractor",
                    "code" => "property.get_all_sub_contractor",
                ],
                [
                    "label" => "Menu Asset Management",
                    "code" => "asset.manage_assets",
                ],
                [
                    "label" => "Menu Cash In",
                    "code" => "finance.manage_cash_in",
                ],
                [
                    "label" => "Menu Cash Out",
                    "code" => "finance.manage_cash_out",
                ],
                [
                    "label" => "Menu Submission",
                    "code" => "finance.get_all_submissions",
                ],
                [
                    "label" => "Menu Cash Flow",
                    "code" => "finance.view_cash_flow",
                ],
                [
                    "label" => "Menu Laba Rugi",
                    "code" => "finance.view_laba_rugi",
                ],
                [
                    "label" => "Menu Neraca",
                    "code" => "finance.view_neraca",
                ],
                [
                    "label" => "Menu Bank",
                    "code" => "finance.manage_bank_accounts",
                ],
                [
                    "label" => "Menu Pinjaman",
                    "code" => "finance.manage_debt",
                ],
            ],
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
            "contact" => [
                [
                    "label" => "Get all Contacts",
                    "code" => "contact.get_all",
                ],
                [
                    "label" => "Create Contact",
                    "code" => "contact.create",
                ],
                [
                    "label" => "Update Contact",
                    "code" => "contact.update",
                ],
                [
                    "label" => "Delete Contact",
                    "code" => "contact.delete",
                ],
            ],
            "dashboard" => [
                [
                    "label" => "Get all Marketing Performance",
                    "code" => "marketing.get_marketing_performance",
                ],
                [
                    "label" => "Dashboard Analytics",
                    "code" => "dashboard.get_analytics",
                ],
            ],
            "lead" => [
                [
                    "label" => "Get all Leads",
                    "code" => "lead.get_all",
                ],
                [
                    "label" => "Create Lead",
                    "code" => "lead.create",
                ],
                [
                    "label" => "Update Lead",
                    "code" => "lead.update",
                ],
                [
                    "label" => "Delete Lead",
                    "code" => "lead.delete",
                ],
                [
                    "label" => "Get Reservation Summary",
                    "code" => "lead.get_reservation_summary",
                ],
                [
                    "label" => "Create Reservation",
                    "code" => "lead.create_reservation",
                ],
                [
                    "label" => "Update Reservation",
                    "code" => "lead.update_reservation",
                ],
                [
                    "label" => "Confirm Reservation Payment",
                    "code" => "lead.confirm_reservation_payment",
                ],
                [
                    "label" => "Get All Reservations",
                    "code" => "lead.get_all_reservation",
                ],
                [
                    "label" => "Get All Documents",
                    "code" => "lead.get_all_documents",
                ],
                [
                    "label" => "Upload Document",
                    "code" => "lead.upload_document",
                ],
                [
                    "label" => "Verify Document",
                    "code" => "lead.verify_document",
                ],
                [
                    "label" => "Get All Payment",
                    "code" => "lead.get_all_payment",
                ],
                [
                    "label" => "Create Payment",
                    "code" => "lead.create_payment",
                ],
                [
                    "label" => "Update Payment",
                    "code" => "lead.update_payment",
                ],
                [
                    "label" => "Get All Final Legality",
                    "code" => "lead.get_all_final_legality",
                ],
                [
                    "label" => "Create Final Legality",
                    "code" => "lead.create_final_legality",
                ],
                [
                    "label" => "Update Final Legality",
                    "code" => "lead.update_final_legality",
                ]
            ],
            "survey" => [
                [
                    "label" => "Get all Survey",
                    "code" => "survey.get_all",
                ],
                [
                    "label" => "Create Survey",
                    "code" => "survey.create",
                ],
                [
                    "label" => "Update Survey",
                    "code" => "survey.update",
                ],
                [
                    "label" => "Delete Survey",
                    "code" => "survey.delete",
                ],
            ],
            'property' => [
                [
                    "label" => "Get all Project",
                    "code" => "property.get_all_project",
                ],
                [
                    "label" => "Create Project",
                    "code" => "property.create_project",
                ],
                [
                    "label" => "Update Project",
                    "code" => "property.update_project",
                ],
                [
                    "label" => "Delete Project",
                    "code" => "property.delete_project",
                ],
                [
                    "label" => "Get all Cluster",
                    "code" => "property.get_all_cluster",
                ],
                [
                    "label" => "Create Cluster",
                    "code" => "property.create_cluster",
                ],
                [
                    "label" => "Update Cluster",
                    "code" => "property.update_cluster",
                ],
                [
                    "label" => "Delete Cluster",
                    "code" => "property.delete_cluster",
                ],
                [
                    "label" => "Get all Unit",
                    "code" => "property.get_all_unit",
                ],
                [
                    "label" => "Create Unit",
                    "code" => "property.create_unit",
                ],
                [
                    "label" => "Update Unit",
                    "code" => "property.update_unit",
                ],
                [
                    "label" => "Delete Unit",
                    "code" => "property.delete_unit",
                ],
                [
                    "label" => "Manage Site Plan",
                    "code" => "property.manage_site_plan",
                ],
                [
                    "label" => "Modify Site Plan",
                    "code" => "property.modify_site_plan",
                ],
                [
                    "label" => "Get all Property",
                    "code" => "property.get_all_property",
                ],
                [
                    "label" => "Create Property",
                    "code" => "property.create_property",
                ],
                [
                    "label" => "Update Property",
                    "code" => "property.update_property",
                ],
                [
                    "label" => "Delete Property",
                    "code" => "property.delete_property",
                ],
                [
                    "label" => "Get all Sub Contractor",
                    "code" => "property.get_all_sub_contractor",
                ],
                [
                    "label" => "Create Sub Contractor",
                    "code" => "property.create_sub_contractor",
                ],
                [
                    "label" => "Update Sub Contractor",
                    "code" => "property.update_sub_contractor",
                ],
                [
                    "label" => "Delete Sub Contractor",
                    "code" => "property.delete_sub_contractor",
                ],
                [
                    "label" => "Get all Construction",
                    "code" => "property.get_all_construction",
                ],
                [
                    "label" => "Create Construction",
                    "code" => "property.create_construction",
                ],
                [
                    "label" => "Update Construction",
                    "code" => "property.update_construction",
                ],
                [
                    "label" => "Delete Construction",
                    "code" => "property.delete_construction",
                ],
                [
                    "label" => "Get all Retention Case",
                    "code" => "property.get_all_retention",
                ],
                [
                    "label" => "Create Retention Case",
                    "code" => "property.create_retention",
                ],
                [
                    "label" => "Update Retention Case",
                    "code" => "property.update_retention",
                ],
                [
                    "label" => "Delete Retention Case",
                    "code" => "property.delete_retention",
                ],
            ],
            'finance' => [
                [
                    "label" => "Manage Cash In",
                    "code" => "finance.manage_cash_in",
                ],
                [
                    "label" => "Manage Cash Out",
                    "code" => "finance.manage_cash_out",
                ],
                [
                    "label" => "Get All Submission",
                    "code" => "finance.get_all_submissions"
                ],
                [
                    "label" => "Create Submission",
                    "code" => "finance.create_submission"
                ],
                [
                    "label" => "Approve/Reject Submission",
                    "code" => "finance.approval_submission"
                ],
                [
                    "label" => "View Laba Rugi",
                    "code" => "finance.view_laba_rugi"
                ],
                [
                    "label" => "View Neraca",
                    "code" => "finance.view_neraca"
                ],
                [
                    "label" => "Manage Bank Accounts",
                    "code" => "finance.manage_bank_accounts"
                ],
                [
                    "label" => "View Cash Flow",
                    "code" => "finance.view_cash_flow"
                ],
                [
                    "label" => "Manage Pinjaman",
                    "code" => "finance.manage_debt"
                ],
            ],
            // asset
            'asset' => [
                [
                    "label" => "Manage Assets",
                    "code" => "asset.manage_assets"
                ]
            ]
        ]
    ],
];
