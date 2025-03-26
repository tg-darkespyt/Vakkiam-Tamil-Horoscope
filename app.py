import tkinter as tk
from tkinter import messagebox
from start import getHoroscope
import subprocess

def convert_time_format(tob):
    """Convert 24-hour time to 12-hour format with AM/PM."""
    if ":" in tob:
        try:
            h, m = map(int, tob.split(":"))
            am_pm = "AM" if h < 12 else "PM"
            h = h % 12 or 12
            return f"{h}:{m:02d} {am_pm}"
        except ValueError:
            return "Invalid Time"
    return tob

def submit_data():
    """Get input values and process them."""
    name = name_entry.get().strip()
    dob = dob_entry.get().strip()
    tob = convert_time_format(tob_entry.get().strip())
    pob = pob_entry.get().strip()
    gender = gender_var.get()
    if not (name and dob and tob and pob and gender):
        messagebox.showerror("Error", "All fields are required!")
        return
    with open("horoscope_data.txt", "a") as file:
        file.write(f"{name}, {dob}, {tob}, {pob}, {gender}\n")
    getHoroscope(name, dob, tob, pob, gender)
    messagebox.showinfo("Success", "Horoscope Generated Successfully!")

# ✅ Tkinter UI
root = tk.Tk()
root.title("Abirami Astrology")
root.geometry("400x400")
root.resizable(False, False)

# Heading
tk.Label(root, text="Abirami Astrology", font=("Arial", 16, "bold")).pack(pady=10)

# Name
tk.Label(root, text="Name:", font=("Arial", 12)).pack()
name_entry = tk.Entry(root, font=("Arial", 12))
name_entry.pack(pady=5)

# Date of Birth
tk.Label(root, text="Date of Birth (DD-MM-YYYY):", font=("Arial", 12)).pack()
dob_entry = tk.Entry(root, font=("Arial", 12))
dob_entry.pack(pady=5)

# Time of Birth
tk.Label(root, text="Time of Birth (24-hour format HH:MM):", font=("Arial", 12)).pack()
tob_entry = tk.Entry(root, font=("Arial", 12))
tob_entry.pack(pady=5)

# Place of Birth
tk.Label(root, text="Place of Birth:", font=("Arial", 12)).pack()
pob_entry = tk.Entry(root, font=("Arial", 12))
pob_entry.pack(pady=5)

# Gender
tk.Label(root, text="Gender:", font=("Arial", 12)).pack()
gender_var = tk.StringVar(value="Male")
tk.Radiobutton(root, text="Male", variable=gender_var, value="Male", font=("Arial", 12)).pack()
tk.Radiobutton(root, text="Female", variable=gender_var, value="Female", font=("Arial", 12)).pack()

# Submit Button
submit_btn = tk.Button(root, text="Generate Horoscope", font=("Arial", 14), command=submit_data)
submit_btn.pack(pady=10)

# Exit Button
exit_btn = tk.Button(root, text="Exit", font=("Arial", 14), command=root.quit)
exit_btn.pack(pady=10)

root.mainloop()