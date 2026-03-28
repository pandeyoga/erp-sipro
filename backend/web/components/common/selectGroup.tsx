export type OptionData = Record<string, {[key:string] : any}[]>;
interface Props {
    data: OptionData | undefined
    register: any
    label : string
    name: string
}

const SelectGroup: React.FC<Props> = ({ label, data, register, name }) => {
    if(!data) return null;
    // return JSON.stringify(Object.entries(data))
    return (
      <select
        name={name}
        className="select select-bordered w-full"
        {...register(name, {
              required: `${label} wajib dipilih`,
            })}
      >
        <option value="" disabled>
          Pilih {label}
        </option>
        {Object.entries(data).map(([group, children]) => 
            <optgroup key={`${group}`} label={`${group}`}>
              {children.map((option) => (
                <option key={option.id} value={option.id}>
                  {option.name}
                </option>
              ))}
            </optgroup>
        )}
      </select>
    );
  };

  export default SelectGroup