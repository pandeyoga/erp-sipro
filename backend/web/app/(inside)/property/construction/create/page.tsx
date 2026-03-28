'use client'
import axios from "@/lib/axios";
import KprForm from "../form"; // Pastikan kamu punya file ini
import { useRouter } from "next/navigation";
import { useState } from "react";
import toast from "react-hot-toast";
import ConstractionForm from "../form";

export default function CreateLegalitasAkhirPage() {
  const router = useRouter()
  return (
    <div>
      <h1 className="text-xl font-bold text-gray-600 mb-6">Create Constraction</h1>
      <ConstractionForm mode="create" />
    </div>
  )
}
