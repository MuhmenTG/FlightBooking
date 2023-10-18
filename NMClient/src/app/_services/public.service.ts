import { Injectable } from '@angular/core';
import { FAQS } from '../_models/FAQS';
import { HttpService } from './http.service';
import { environment } from 'src/environments/environment.development';
import { Observable } from 'rxjs';
import { CitySearchResponse } from '../_models/Flights/CitySearchResponse';
import { FinalBookingResponse } from '../_models/Flights/FinalBookingResponse';
import { EnquiryRequest } from '../_models/Enquiries/EnquiryRequest';

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

  resendEmail(bookingReference: string, email: string): Observable<any> {
    return this.http.post<any>(this.apiUrl + "/resendBookingConfirmationPDF", { bookingReference, email })
  }

  retrieveBooking(bookingReferenceId: string): Observable<FinalBookingResponse> {
    return this.http.get<FinalBookingResponse>(this.apiUrl + "/retrieveBooking/" + bookingReferenceId)
  }

  getCityname(searchString: string): Observable<CitySearchResponse> {
    return this.http.get<CitySearchResponse>(this.apiUrl + "/getCityName/" + searchString)
  }
}
