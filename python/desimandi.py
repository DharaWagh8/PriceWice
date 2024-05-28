import requests 
from bs4 import BeautifulSoup 
import pandas as pd 
import json

# response = requests.get("https://www.walmart.ca/en/ip/tomato-roma/6000191272055") 
# print(response.status_code) 

# soup = BeautifulSoup(response.content, 'html.parser') 
# print(soup.get_text())

# Function to extract script Tags
def scriptTags(soup):
	try:
		all_data = soup.find_all("script", {"type": "application/ld+json"})
		jsn = {}
		i = 1
        
		for data in all_data:
			jsn = json.loads(data.string)
			jsn.update(jsn)
			break
			#print(jsn)
			#print(json.dumps(jsn, indent = 4))

	except AttributeError:
		jsn = "Not Available"	

	return jsn	


walmart_product_url = 'https://www.desimandi.ca/desi-guava-' 
headers = {'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.0.0 Safari/537.36'} 

response = requests.get(walmart_product_url, headers=headers) 
soup = BeautifulSoup(response.content, 'html.parser') 
#print(soup.prettify())

title = soup.find("h2").text
print(title)
#links = soup.select("div", 'div.product-price .price').text
links = soup.find("span", attrs={'class':'price'}).text
print(links)
#scriptJson = scriptTags(soup)
#price = scriptJson['offers']['price']
#print(price)
# span_tag = soup.find('span', {'itemprop': 'price'})
# print(span_tag)

# if span_tag:
#     price_text = span_tag.text
#     # Remove non-numeric characters from the text (e.g., ¢)
#     price_value = ''.join(filter(str.isdigit, price_text))
#     print("Price:", price_value)
# else:
#     print("No span tag with itemprop='price' found.")

product_data = [{ 
"title": title, 
"price": "", 
}]


