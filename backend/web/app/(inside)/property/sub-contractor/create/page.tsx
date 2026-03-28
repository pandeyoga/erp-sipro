'use client'
import { useRouter } from "next/navigation";
import SubContractor from "../form";

export default function CreateSubContractorPage() {
  return (
    <div>
      <h1 className="text-xl font-bold text-gray-600 mb-6">Create Sub Contractor</h1>
      <SubContractor mode="create" />
    </div>
  )
}
