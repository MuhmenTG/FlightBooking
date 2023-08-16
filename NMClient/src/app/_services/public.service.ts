import { Injectable } from '@angular/core';
import { FAQS } from '../_models/FAQS';
import { HttpService } from './http.service';
import { environment } from 'src/environments/environment.development';
import { Observable } from 'rxjs';
import { EnquiryRequest } from '../_models/EnquiryRequest';

@Injectable({
  providedIn: 'root'
})
export class PublicService extends HttpService {
  private apiUrl = environment.apiUrl + "/public";

  getAllFaqs(): Observable<FAQS> {
    return this.http.get<FAQS>(this.apiUrl + "/getAllFaqs");
  }

  getSpecificFaq(faqId: number): Observable<FAQS> {
    return this.http.get<FAQS>(this.apiUrl + "/getSpecificFaq/" + faqId)
  }

  sendEnquiry(enquiryRequest: EnquiryRequest): Observable<any> {
    return this.http.post<any>(this.apiUrl + "/contactform", enquiryRequest)
  }

  retrieveBooking(bookingReferenceId: string): Observable<any> {
    return this.http.get<any>(this.apiUrl + "/retriveBooking/" + bookingReferenceId)
  }

  getCityname(searchString: string): Observable<string> {
    return this.http.get<string>(this.apiUrl + "/getCityName/" + searchString)
  }

}
