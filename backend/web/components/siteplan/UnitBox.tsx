'use client';

import { useEffect, useRef, useState } from 'react';
import * as MoveableComponent from 'react-moveable';

import { cn } from '@/lib/utils';
import { UnitBoxProps } from '@/types/unit';
import { useSitemapContext } from '@/context/useSitemapContext';

const Moveable = MoveableComponent.default;


type UnitBoxExtendedProps = UnitBoxProps & {
  setSelectedUnitId : (value : string | null) => void,
  selectedUnitId : string | null;
  zoom: number
};

export function UnitBox({
  id,
  label,
  top,
  left,
  width = 80,
  height = 80,
  rotate,
  status,
  selectedUnitId,
  setSelectedUnitId,
  onClick,
  zoom
}: UnitBoxExtendedProps) {
  const { editMode, updateUnitPosition, updateUnitById, convertToPx, convertToPercent } = useSitemapContext();
  const ref = useRef<HTMLDivElement>(null);
  const [frame] = useState({ translate: [0, 0] });

  const [rotateControl,setRotate] = useState(rotate);
  const [handleSize,sethandleSize] = useState<number | null>(null);

  useEffect(()=>{
    setRotate(rotate)
  },[width,height])


  const handleDrag = ({ beforeTranslate }: any) => {
    frame.translate = beforeTranslate;
    ref.current!.style.transform = `translate(${beforeTranslate[0]}px, ${beforeTranslate[1]}px) rotate(${rotate}deg)`;
  };

  const handleDragEnd = ({ lastEvent }: any) => {
    if (lastEvent) {
      const [dx, dy] = frame.translate;
  
      // 1. Update posisi absolut
      const newLeft = convertToPx(left,'left') + dx;
      const newTop = convertToPx(top,'top') + dy;
  
      updateUnitPosition(id, convertToPercent(newLeft,'left'), convertToPercent(newTop,'top'), rotate ?? 0);
      
      setSelectedStatus(false);
  
      // 2. Reset transform, karena posisi sudah berpindah via top/left
      frame.translate = [0, 0];
      ref.current!.style.transform = `rotate(${rotate}deg)`;
      
    }
  };
  
  

  const handleResize = ({ width: w, height: h, drag }: any) => {
    const [dx, dy] = drag?.beforeTranslate;
    frame.translate = [dx, dy];
    ref.current!.style.width = `${w}px`;
    ref.current!.style.height = `${h}px`;
    const baseSize = (w + h) / 2;
    sethandleSize(baseSize * 0.1)
    ref.current!.style.transform = `translate(${dx}px, ${dy}px) rotate(${rotate}deg)`;
    // console.log("HOSAD")
  };

  useEffect(()=>{
    const baseSize = (width + height) / 2;
    let default_handle_size = Math.max(baseSize * 0.1, 4)

    ref.current!.style["--handle-size" as any] = `${handleSize}px`;
    const controls = document.querySelectorAll(".moveable-control");
    controls.forEach((el) => {
      (el as HTMLElement).style.width = `${handleSize ?? default_handle_size}px`;
      (el as HTMLElement).style.height = `${handleSize ?? default_handle_size}px`;
      (el as HTMLElement).style.margin = `calc(${handleSize}px / -2) 0 0 calc(${handleSize}px / -2)`;
    });

  },[handleSize])
  
  const handleResizeEnd = ({ lastEvent }: any) => {
    const { width: w, height: h, drag } = lastEvent
    updateUnitById(id ?? "", { width: convertToPercent(w,'width'),height: convertToPercent(h,'height') });
    const baseSize = (w + h) / 2;
    sethandleSize(Math.max(baseSize * 0.1, 4))
    // if (drag && drag.beforeTranslate) {
    //   alert("resize end 3")
    //   const [dx, dy] = drag.beforeTranslate;
    //   updateUnitById(id ?? "", { width: w,height: h });
    //   updateUnitPosition(id,left + dx,top + dy,rotate ?? 0)
    //   frame.translate = [0, 0];
    //   ref.current!.style.transform = `rotate(${rotate}deg)`;
    // }
  };

  const handleRotate = ({ beforeRotate }: any) => {
    ref.current!.style.transform = `rotate(${beforeRotate}deg)`;
    
    setRotate(beforeRotate)
    
  };

  const handleRotateEnd = ({ lastEvent }: any) => {
    updateUnitById(id, { rotate : lastEvent.beforeRotate});
  };
  const [selectedStatus, setSelectedStatus] = useState<boolean>(false);



  return (
    <>
      <div
        ref={ref}
        onClick={(e) => {
          e.stopPropagation(); // agar tidak kena klik ke luar
          setSelectedStatus(!selectedStatus);
          if(!selectedStatus){
            setSelectedUnitId(id);
            if(onClick){
              onClick(id)
            }
          }else{
            setSelectedUnitId(null);
            if(onClick){
              onClick("")
            }
          }
        }}
        className={cn(
          'tooltip absolute rounded font-medium flex items-center justify-center select-none border-3 text-gray-700 font-bold text-xs',
          status === 'belum_dibangun' && 'bg-gray-400/30 border-gray-400',
          status === 'under_development' && 'bg-blue-500/30 border-blue-500',
          status === 'sold' && 'bg-red-500/30 border-red-600',
          status === 'retention' && 'bg-yellow-500/30 border-yellow-500',
          status === 'available' && 'bg-green-500/30 border-green-500'
        )}
        data-tip={`Unit: ${label ?? id} (${status})`}
        style={{
          top : convertToPx(top,'top'),
          left : convertToPx(left,'left'),
          width : convertToPx(width,'width'),
          height : convertToPx(height,'height'),
          transform: `rotate(${rotate}deg)`,
          transformOrigin: 'center',
          zIndex: 10
        }}
      >
        {label ?? id}
      </div>

      {selectedStatus && selectedUnitId == id&& (
        // @ts-ignore
        <Moveable
          target={ref}
          draggable
          resizable
          rotatable
          onDrag={ editMode ? handleDrag : undefined}
          onDragEnd={editMode ? handleDragEnd : undefined}
          onResize={editMode ? handleResize : undefined}
          onResizeEnd={editMode ? handleResizeEnd : undefined}
          onRotate={editMode ? handleRotate : undefined}
          onRotateEnd={editMode ? handleRotateEnd : undefined}
          origin={false}
          keepRatio={false}
          edge={false}
        />
      )}
    </>
  );
}
