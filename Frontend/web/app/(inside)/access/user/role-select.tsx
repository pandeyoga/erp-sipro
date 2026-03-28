'use client';

import { useEffect, useState } from 'react';
import { UseFormRegister, FieldValues, FieldErrors } from 'react-hook-form';
import { User } from './page';
import axios from '@/lib/axios';

type Role = {
  id: string;
  name: string;
};

type RoleFieldsetProps = {
  register: UseFormRegister<User>;
  errors: FieldErrors<User>
};

export default function RoleFieldset({ register, errors }: RoleFieldsetProps) {
  const [roles, setRoles] = useState<Role[]>([]);

  useEffect(() => {
    const fetchRoles = async () => {
      try {
        const response = await axios.get('/manage/role/select');
        if (response.data.success) {
          setRoles(response.data.data);
        }
      } catch (error) {
        console.error('Gagal mengambil data role:', error);
      }
    };

    fetchRoles();
  }, []);

  return (
    <fieldset className="fieldset w-full">
        <legend className="fieldset-legend">Role</legend>
        <select defaultValue="" {...register("role_id", { required: true })} className="select w-full">
            <option disabled={true}>Choose role</option>
            {roles.map((role) => (<option key={role.id} value={role.id}>{role.name} </option> ))}
        </select>
        {errors.role_id && <p className="text-red-500 text-sm">Wajib diisi</p>}
    </fieldset>
  );
}
