##  To run this file : python CountryData.py
##  Will spit out Json output.

import urllib
import json


label_contract_amt = "ContractAmt"
label_borrower_country_name = "CountryName"
label_borrower_country_code = "CountryCd"
label_supplier_country_name = "SupplierCountry"
label_in = "InOut"
label_total_in_amount = "TotalInAmt"
label_total_out_amount= "TotalOutAmt"

def getAllContracts(contract_url):
"""
This function calls the required API to pull data from the external service.
"""
    str_output = urllib.urlopen(contract_url).read()
    jdata=json.loads(str_output)
    #data_dict={}
    row_list= jdata['data']
    return  row_list

def parse_contract_row(row):
"""
Each row pulled from the API is parsed here
"""
    fld_calendar_date=8
    fld_fiscal_year=9
    fld_region=10
    fld_borrower_country_name=11
    fld_borrower_country_cd=12
    fld_project_id=13
    fld_project_name=14
    fld_main_loan_credit=15
    fld_procurement_type=16
    fld_procurement_cat=17
    fld_proc_method=18
    fld_wb_contract_no=19
    fld_wb_contract_desc=20
    fld_wb_contract_refno=21
    fld_contract_dt=22
    fld_supplier=23
    fld_supplier_country=24
    fld_total_contract_amt=25
    
    borrower_country_cd = row[fld_borrower_country_cd]
    borrower_country_name = row[fld_borrower_country_name]
    supplier_country_name=row[fld_supplier_country]
    contract_amount = row[fld_total_contract_amt];
    
    #print "Country_name = " + borrower_country_name                     
    
    mydata={label_borrower_country_code: row[fld_borrower_country_cd], 
            label_borrower_country_name: borrower_country_name,
            label_supplier_country_name: supplier_country_name,
            label_contract_amt: contract_amount
            }
    return mydata

def build_output_rows():
"""
This function builds the result set , in a format suitable for summarization
"""
    row_list=getAllContracts("https://finances.worldbank.org/api/views/kdui-wcs3/rows.json")
    #print "Num contracts:" + str(len(row_list))
    result_dict={}
    for i in row_list:
        contract_dict=parse_contract_row(i)
        country_code = contract_dict[label_borrower_country_code]
        result_array = result_dict.get(country_code, [])
        
        # if supplier == borrower then its in, else out
        value_in_out="In"
        if contract_dict[label_borrower_country_name] != contract_dict[label_supplier_country_name] :
            #print contract_dict[label_borrower_country_name] + ' : ' + contract_dict[label_supplier_country_name]
            value_in_out="Out"
        temp_dict = {
            label_borrower_country_name : contract_dict[label_borrower_country_name],
            label_supplier_country_name : contract_dict[label_supplier_country_name],     
            label_contract_amt : contract_dict[label_contract_amt] ,
            label_in : value_in_out
        }
        result_array.append(temp_dict)
        result_dict[country_code] = result_array
        #print contract_dict
        #print i    
    #print "Num countries : " + str(len(result_dict))    
    return result_dict


def summarizeByCountry(mydict):
"""
This function summarizes the data as required
"""
    summary_dict={}
    count_countries=0
    for i in mydict.keys():
        country = i
        country_name = ""
        country_array = mydict[i]
        totalInAmount=0
        totalOutAmount=0
        for ii in country_array:
            country_dict=ii
            country_name=country_dict[label_borrower_country_name]
            contract_amount_numeric = round( float(country_dict[label_contract_amt].encode('ascii', 'ignore')))
            if country_dict[label_in] == "In":
                totalInAmount += contract_amount_numeric
            else:
                totalOutAmount += contract_amount_numeric
        print "country_name:" + country_name        
        summary_dict[country]= {
            label_borrower_country_name : country_name,
            label_total_in_amount: totalInAmount,
            label_total_out_amount:totalOutAmount }
    return summary_dict
    

if __name__ == "__main__":
    result=build_output_rows()
    #print result
    sum_dict=summarizeByCountry(result)
    #print sum_dict
    print json.dumps(sum_dict)
    
