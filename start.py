import time
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.common.keys import Keys
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC

def getHoroscope(name, dob, tob, pob, gender):
    chrome_options = webdriver.ChromeOptions()
    # chrome_options.add_argument("--headless")  # Run in background
    driver = webdriver.Chrome(options=chrome_options)
    JATHAGAM_URL = "https://srirangaminfo.com/Jathagam-tamil.php"
    driver.get(JATHAGAM_URL)
    b_date, b_month, b_year = dob.split("-")
    user_data = {
        "name": name,
        "bd": b_date,
        "bm": b_month,
        "by": b_year,
        "tob": tob.split()[0],
        "am_pm": tob.split()[1],
        "gender": gender,
        "place": pob
    }
    driver.find_element(By.NAME, "jname").send_keys(user_data["name"])
    date = driver.find_element(By.ID, "ju_dd")
    date.click()
    date.send_keys(int("04"))
    b_time = driver.find_element(By.ID, "ju_mm")
    b_time.click()
    b_time.send_keys(user_data["bm"])
    year = driver.find_element(By.ID, "ju_yy")
    year.click()
    year.send_keys(user_data["by"])
    time_input = driver.find_element(By.NAME, "DOBTime")
    time_input.send_keys(user_data["tob"])
    time_input.send_keys(user_data["am_pm"])
    location = driver.find_element(By.NAME, "location")
    location.click()
    location.send_keys(user_data["place"])
    time.sleep(2)
    elements = driver.find_elements(By.CLASS_NAME, "getv")
    for element in elements:
        if user_data["place"] in element.text:
            element.click()
            print(f"Clicked on: {element.text}")
            break
    gender_dropdown = driver.find_element(By.NAME, "Jgen")
    for option in gender_dropdown.find_elements(By.TAG_NAME, "option"):
        if user_data["gender"] in option.text:
            option.click()
            break
    time.sleep(10)
    driver.find_element(By.ID, "submit").click()
    try:
        WebDriverWait(driver, 15).until(
            EC.presence_of_element_located((By.ID, "prnt"))
        )
        driver.find_element(By.ID, "prnt").click()
        driver.switch_to.window(driver.window_handles[-1])
        modify_script = """
        document.querySelector('svg').style.display = 'none';
        document.body.innerHTML = document.body.innerHTML.replace(
            "To Check Marriage Match, Horoscope - Sriranga Jothida Nilayam - Whatsapp / Call : +91 9442054021",
            "To get your Horoscope, Contact Abirami Astrology Call : +91 98431 96121"
        );
        document.body.innerHTML = document.body.innerHTML.replace(
            "To Advertise Here Contact - +919442054021",
            "Abirami Astrology, K.G.Chavadi, Coimbatore - 641105"
        );
        document.body.innerHTML = document.body.innerHTML.replace(
            "Jathgam More Details - Scan", 
            ""
        );
        document.body.innerHTML = document.body.innerHTML.replace(
            "This Jathgam computed from Srirangaminfo.com Free online Jathagam WEB-APP/ Software @  https://srirangaminfo.com/Jathagam-tamil.php",
            "This Horoscope was computed from Abirami Astrology"
        );
        document.title = document.title.replace("SrirangamInfo.com Online Jathagam", "Abirami Astrology");
        """
        driver.execute_script(modify_script)
        time.sleep(3)
        driver.execute_script("window.print();")
        time.sleep(300)
    except Exception as e:
        print(f"Error: {e}.")
