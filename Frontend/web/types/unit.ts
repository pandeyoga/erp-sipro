export type UnitStatus = "available" | "sold" | "reserved";

export type UnitBoxProps = {
    id: string;
    property_id?: string;
    left: number;
    top: number;
    label?: string;
    status?: string;
    width?: number;    // in pixels
    height?: number;   // in pixels
    rotate?: number;   // in degrees
    onDragEnd?: (id: string, x: number, y: number) => void;
    onClick?: (id: string) => void;
  };
  