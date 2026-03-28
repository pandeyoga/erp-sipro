"use client";

import { useForm, Controller } from "react-hook-form";
import { useEffect, useState } from "react";
import axios from "@/lib/axios";

type Permission = {
  label: string;
  code: string;
};

type PermissionData = {
  [module: string]: Permission[];
};

type FormValues = {
  permissions: string[];
};

type PropsPermisson = { 
  setPermission : (value : Array<string>)=> void,
  defaultValues : {
    name: string;
    code : string;
  }[] | any[]
}


export default function PermissionForm({ setPermission, defaultValues } : PropsPermisson  ) {
  const { control, handleSubmit,setValue, watch } = useForm<FormValues>({
    defaultValues: {
      permissions: [],
    },
  });

  const [isLoaded, setIsLoaded] = useState(false)

  useEffect(()=>{
    if(isLoaded){
      setPermission(watch("permissions"))
    }
  },[watch("permissions")])

  async function getPermissionCheck(){
    const permison_checked: string[] =  await Promise.all(
      defaultValues.map(async (value) => {
        return value.code;
      })
    );
    await setValue("permissions", permison_checked);
    setIsLoaded(true);
  }
  useEffect(()=>{
    if(defaultValues && (!isLoaded || defaultValues.length != watch("permissions").length)){
      getPermissionCheck()
    }
  },[defaultValues])

  const [permissionData, setPermissionData] = useState<PermissionData>({});

  useEffect(() => {
    const fetchPermissions = async () => {
      try {
        const response = await axios.get("/manage/role/permissions", {
          headers: {
            "Content-Type": "application/json",
          },
        });
        const basePermissions: PermissionData = response.data.data.base;
        setPermissionData(basePermissions);
      } catch (error) {
        console.error("Failed to fetch permissions:", error);
      }
    };

    fetchPermissions();
  }, []);

  return (
    <div>
      {Object.entries(permissionData).map(([moduleName, permissions]) => (
        <div key={moduleName} className="card bg-base-200">
          <div className="card-body">
            <h2 className="card-title capitalize">{moduleName}</h2>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
              {permissions.map((permission) => (
                <Controller
                  key={permission.code}
                  name="permissions"
                  control={control}
                  render={({ field }) => (
                    <label className="label cursor-pointer flex items-center space-x-2">
                      <input
                        type="checkbox"
                        className="checkbox"
                        value={permission.code}
                        checked={field.value.includes(permission.code)}
                        onChange={(e) => {
                          const checked = e.target.checked;
                          const value = permission.code;
                          field.onChange(
                            checked
                              ? [...field.value, value]
                              : field.value.filter((v) => v !== value)
                          );
                        }}
                      />
                      <span className="label-text">{permission.label}</span>
                    </label>
                  )}
                />
              ))}
            </div>
          </div>
        </div>
      ))}
    </div>
  );
}
